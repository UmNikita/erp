<?php

namespace App\Accounting\RestAPIController;

use App\Entity\AccountingAllocation;
use App\Entity\AccountingAudit;
use App\Entity\AccountingCategory;
use App\Entity\AccountingTransaction;
use App\Entity\User;
use App\Repository\AccountingAllocationRepository;
use App\Repository\AccountingAuditRepository;
use App\Repository\AccountingCategoryRepository;
use App\Repository\AccountingTransactionRepository;
use App\Repository\UserRepository;
use App\Security\Enum\Permission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

#[Route('/accounting')]
final class AccountingApiController extends AbstractController
{
    #[Route('/bootstrap', methods: ['GET'])]
    public function bootstrap(
        Request $request,
        AccountingCategoryRepository $categories,
        AccountingTransactionRepository $transactions,
        AccountingAuditRepository $audit,
        UserRepository $users,
    ): JsonResponse {
        $this->assertAccess();

        $from = $this->dateFromQuery($request->query->get('from'));
        $to = $this->dateFromQuery($request->query->get('to'));

        return $this->json([
            'categories' => array_map($this->categoryData(...), $categories->findBy([], ['active' => 'DESC', 'id' => 'ASC'])),
            'transactions' => array_map($this->transactionData(...), $transactions->findForPeriod($from, $to)),
            'audit' => array_map($this->auditData(...), $audit->findRecent()),
            'users' => array_map(static fn (User $user) => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ], $users->findBy(['is_active' => true], ['name' => 'ASC'])),
            'currentUser' => $this->userData($this->currentUser()),
        ]);
    }

    #[Route('/transactions', methods: ['POST'])]
    public function createTransaction(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->assertAccess();
        $data = $this->requestData($request);
        $type = $this->transactionType($data['type'] ?? null);
        $amount = $this->amount($data['amount'] ?? null);
        $date = $this->requiredDate($data['date'] ?? null);
        $counterparty = $this->optionalText($data['counterparty'] ?? null, 180) ?? '';
        $user = $this->currentUser();

        $transaction = new AccountingTransaction($type, $amount, $date, $counterparty, $user);
        $transaction->setComment($this->optionalText($data['comment'] ?? null, 1000));
        $transaction->setIncluded((bool) ($data['included'] ?? true));
        $em->persist($transaction);
        $em->flush();

        $this->recordAudit($em, 'transaction.created', 'transaction', $transaction->getId(), [
            'type' => $type,
            'amount' => (float) $amount,
            'counterparty' => $counterparty,
        ]);
        $em->flush();

        return $this->json($this->transactionData($transaction), Response::HTTP_CREATED);
    }

    #[Route('/transactions/{id}', methods: ['PATCH'])]
    public function updateTransaction(
        int $id,
        Request $request,
        AccountingTransactionRepository $transactions,
        AccountingCategoryRepository $categories,
        UserRepository $users,
        EntityManagerInterface $em,
    ): JsonResponse {
        $this->assertAccess();
        $transaction = $transactions->find($id);
        if (!$transaction) return $this->json(['error' => 'Operation not found'], Response::HTTP_NOT_FOUND);

        $data = $this->requestData($request);
        $before = $this->transactionSnapshot($transaction);
        $oldType = $transaction->getType();
        $oldAmount = (float) $transaction->getAmount();
        $allocated = $this->allocatedAmount($transaction);

        if (array_key_exists('type', $data)) $transaction->setType($this->transactionType($data['type']));
        if (array_key_exists('amount', $data)) $transaction->setAmount($this->amount($data['amount']));
        if (array_key_exists('date', $data)) $transaction->setOccurredAt($this->requiredDate($data['date']));
        if (array_key_exists('counterparty', $data)) $transaction->setCounterparty($this->optionalText($data['counterparty'], 180) ?? '');
        if (array_key_exists('comment', $data)) $transaction->setComment($this->optionalText($data['comment'], 1000));
        if (array_key_exists('included', $data)) $transaction->setIncluded((bool) $data['included']);
        if (array_key_exists('createdById', $data)) {
            $createdBy = $data['createdById'] ? $users->find((int) $data['createdById']) : null;
            if ($data['createdById'] && !$createdBy) return $this->json(['error' => 'User not found'], Response::HTTP_UNPROCESSABLE_ENTITY);
            $transaction->setCreatedBy($createdBy);
        }

        if ($oldType !== $transaction->getType()) {
            $transaction->clearAllocations();
        } elseif (count($transaction->getAllocations()) === 1 && abs($allocated - $oldAmount) < 0.01) {
            $transaction->getAllocations()->first()->setAmount($transaction->getAmount());
        } elseif ($allocated > (float) $transaction->getAmount()) {
            $transaction->clearAllocations();
        }

        if (array_key_exists('categoryId', $data)) {
            $transaction->clearAllocations();
            if ($data['categoryId']) {
                $category = $categories->find((int) $data['categoryId']);
                if (!$category || $category->getType() !== $transaction->getType()) {
                    return $this->json(['error' => 'Category does not match operation type'], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $transaction->addAllocation(new AccountingAllocation($category, $transaction->getAmount()));
            }
        }

        $this->recordAudit($em, 'transaction.updated', 'transaction', $transaction->getId(), [
            'before' => $before,
            'after' => $this->transactionSnapshot($transaction),
        ]);
        $em->flush();

        return $this->json($this->transactionData($transaction));
    }

    #[Route('/transactions/{id}/allocate', methods: ['POST'])]
    public function allocate(
        int $id,
        Request $request,
        AccountingTransactionRepository $transactions,
        AccountingCategoryRepository $categories,
        EntityManagerInterface $em,
    ): JsonResponse {
        $this->assertAccess();
        $transaction = $transactions->find($id);
        if (!$transaction) return $this->json(['error' => 'Operation not found'], Response::HTTP_NOT_FOUND);

        $data = $this->requestData($request);
        $category = $categories->find((int) ($data['categoryId'] ?? 0));
        if (!$category || !$category->isActive() || $category->getType() !== $transaction->getType()) {
            return $this->json(['error' => 'Category does not match operation type'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $amount = $this->amount($data['amount'] ?? null);
        $remaining = (float) $transaction->getAmount() - $this->allocatedAmount($transaction);
        if ((float) $amount > $remaining + 0.001) {
            return $this->json(['error' => 'Allocation exceeds the remaining amount'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $existing = $transaction->getAllocations()->filter(
            static fn (AccountingAllocation $allocation) => $allocation->getCategory()->getId() === $category->getId()
        )->first();
        if ($existing) $existing->setAmount(number_format((float) $existing->getAmount() + (float) $amount, 2, '.', ''));
        else $transaction->addAllocation(new AccountingAllocation($category, $amount));

        $this->recordAudit($em, 'transaction.allocated', 'transaction', $transaction->getId(), [
            'categoryId' => $category->getId(),
            'category' => $category->getName(),
            'amount' => (float) $amount,
        ]);
        $em->flush();

        return $this->json($this->transactionData($transaction));
    }

    #[Route('/categories', methods: ['POST'])]
    public function createCategory(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->assertAccess();
        $data = $this->requestData($request);
        $category = new AccountingCategory(
            $this->requiredText($data['name'] ?? null, 'name', 120),
            $this->transactionType($data['type'] ?? null),
            $this->currentUser(),
        );
        $category->setIcon($this->requiredText($data['icon'] ?? '✨', 'icon', 16));
        $category->setColor($this->color($data['color'] ?? '#3f7af6'));
        $em->persist($category);
        $em->flush();
        $this->recordAudit($em, 'category.created', 'category', $category->getId(), ['name' => $category->getName()]);
        $em->flush();

        return $this->json($this->categoryData($category), Response::HTTP_CREATED);
    }

    #[Route('/categories/{id}', methods: ['PATCH'])]
    public function updateCategory(int $id, Request $request, AccountingCategoryRepository $categories, EntityManagerInterface $em): JsonResponse
    {
        $this->assertAccess();
        $category = $categories->find($id);
        if (!$category) return $this->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        $data = $this->requestData($request);
        $before = $this->categorySnapshot($category);
        if (array_key_exists('name', $data)) $category->setName($this->requiredText($data['name'], 'name', 120));
        if (array_key_exists('icon', $data)) $category->setIcon($this->requiredText($data['icon'], 'icon', 16));
        if (array_key_exists('color', $data)) $category->setColor($this->color($data['color']));
        if (array_key_exists('active', $data)) $category->setActive((bool) $data['active']);
        $this->recordAudit($em, 'category.updated', 'category', $category->getId(), [
            'before' => $before,
            'after' => $this->categorySnapshot($category),
        ]);
        $em->flush();

        return $this->json($this->categoryData($category));
    }

    #[Route('/categories/{id}', methods: ['DELETE'])]
    public function deleteCategory(
        int $id,
        AccountingCategoryRepository $categories,
        AccountingAllocationRepository $allocations,
        EntityManagerInterface $em,
    ): JsonResponse {
        $this->assertAccess();
        $category = $categories->find($id);
        if (!$category) return $this->json(['error' => 'Category not found'], Response::HTTP_NOT_FOUND);
        if ($category->isActive() || $allocations->count(['category' => $category]) > 0) {
            return $this->json(['error' => 'Only an unused archived category can be deleted'], Response::HTTP_CONFLICT);
        }
        $name = $category->getName();
        $categoryId = $category->getId();
        $em->remove($category);
        $this->recordAudit($em, 'category.deleted', 'category', $categoryId, ['name' => $name]);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/audit', methods: ['GET'])]
    public function audit(AccountingAuditRepository $audit): JsonResponse
    {
        $this->assertAccess();
        return $this->json(array_map($this->auditData(...), $audit->findRecent(500)));
    }

    private function assertAccess(): void { $this->denyAccessUnlessGranted(Permission::ACCOUNTING_ACCESS->value); }
    private function currentUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) throw $this->createAccessDeniedException();
        return $user;
    }

    private function requestData(Request $request): array
    {
        try { $data = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new BadRequestHttpException('Invalid JSON'); }
        return is_array($data) ? $data : [];
    }

    private function transactionType(mixed $value): string
    {
        if (!in_array($value, ['income', 'expense'], true)) throw new UnprocessableEntityHttpException('Invalid operation type');
        return $value;
    }

    private function amount(mixed $value): string
    {
        if (!is_numeric($value) || (float) $value <= 0) throw new UnprocessableEntityHttpException('Amount must be greater than zero');
        return number_format((float) $value, 2, '.', '');
    }

    private function requiredDate(mixed $value): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) $value);
        if (!$date || $date->format('Y-m-d') !== $value) throw new UnprocessableEntityHttpException('Invalid date');
        return $date;
    }

    private function dateFromQuery(?string $value): ?\DateTimeImmutable
    {
        return $value ? $this->requiredDate($value) : null;
    }

    private function requiredText(mixed $value, string $field, int $max): string
    {
        $value = trim((string) $value);
        if ($value === '' || mb_strlen($value) > $max) throw new UnprocessableEntityHttpException(sprintf('Invalid %s', $field));
        return $value;
    }

    private function optionalText(mixed $value, int $max): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        if (mb_strlen($value) > $max) throw new UnprocessableEntityHttpException('Text is too long');
        return $value;
    }

    private function color(mixed $value): string
    {
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', (string) $value)) throw new UnprocessableEntityHttpException('Invalid color');
        return strtolower((string) $value);
    }

    private function allocatedAmount(AccountingTransaction $transaction): float
    {
        return array_reduce($transaction->getAllocations()->toArray(), static fn (float $sum, AccountingAllocation $allocation) => $sum + (float) $allocation->getAmount(), 0.0);
    }

    private function transactionSnapshot(AccountingTransaction $transaction): array
    {
        return [
            'type' => $transaction->getType(),
            'amount' => (float) $transaction->getAmount(),
            'date' => $transaction->getOccurredAt()->format('Y-m-d'),
            'counterparty' => $transaction->getCounterparty(),
            'comment' => $transaction->getComment(),
            'included' => $transaction->isIncluded(),
            'createdById' => $transaction->getCreatedBy()?->getId(),
            'allocations' => array_map(static fn (AccountingAllocation $allocation) => [
                'categoryId' => $allocation->getCategory()->getId(),
                'amount' => (float) $allocation->getAmount(),
            ], $transaction->getAllocations()->toArray()),
        ];
    }

    private function categorySnapshot(AccountingCategory $category): array
    {
        return [
            'name' => $category->getName(),
            'type' => $category->getType(),
            'icon' => $category->getIcon(),
            'color' => $category->getColor(),
            'active' => $category->isActive(),
        ];
    }

    private function recordAudit(EntityManagerInterface $em, string $action, string $entityType, ?int $entityId, array $payload): void
    {
        $em->persist(new AccountingAudit($this->currentUser(), $action, $entityType, $entityId, $payload));
    }

    private function userData(?User $user): ?array
    {
        return $user ? ['id' => $user->getId(), 'name' => $user->getName(), 'email' => $user->getEmail()] : null;
    }

    private function categoryData(AccountingCategory $category): array
    {
        return [
            'id' => $category->getId(), 'name' => $category->getName(), 'type' => $category->getType(),
            'icon' => $category->getIcon(), 'color' => $category->getColor(), 'active' => $category->isActive(),
            'createdAt' => $category->getCreatedAt()->format(DATE_ATOM), 'createdBy' => $this->userData($category->getCreatedBy()),
        ];
    }

    private function transactionData(AccountingTransaction $transaction): array
    {
        return [
            'id' => $transaction->getId(), 'type' => $transaction->getType(), 'amount' => (float) $transaction->getAmount(),
            'date' => $transaction->getOccurredAt()->format('Y-m-d'), 'counterparty' => $transaction->getCounterparty(),
            'comment' => $transaction->getComment(), 'included' => $transaction->isIncluded(),
            'createdBy' => $this->userData($transaction->getCreatedBy()),
            'createdAt' => $transaction->getCreatedAt()->format(DATE_ATOM), 'updatedAt' => $transaction->getUpdatedAt()->format(DATE_ATOM),
            'allocations' => array_map(static fn (AccountingAllocation $allocation) => [
                'id' => $allocation->getId(), 'categoryId' => $allocation->getCategory()->getId(), 'amount' => (float) $allocation->getAmount(),
            ], $transaction->getAllocations()->toArray()),
        ];
    }

    private function auditData(AccountingAudit $audit): array
    {
        return [
            'id' => $audit->getId(), 'user' => $this->userData($audit->getUser()), 'action' => $audit->getAction(),
            'entityType' => $audit->getEntityType(), 'entityId' => $audit->getEntityId(), 'payload' => $audit->getPayload(),
            'createdAt' => $audit->getCreatedAt()->format(DATE_ATOM),
        ];
    }
}
