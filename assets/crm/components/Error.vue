<template>
    <div class="request-error">
        <div class="request-error__icon">!</div>
        <h2 class="request-error__title">Не удалось загрузить данные</h2>
        <p class="request-error__text">Произошла ошибка при получении информации. Проверьте соединение и попробуйте снова.</p>
    </div>
</template>

<style scoped>
.request-error {
    width: calc(100% - 44px);
    min-height: 420px;
    margin: 24px 22px;
    padding: 50px 24px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    text-align: center;
    background: #ffffff;
    border: 1px solid #e2e6ec;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(26, 39, 60, 0.06);
}

.request-error__icon {
    width: 72px;
    height: 72px;
    margin-bottom: 22px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: #dc2626;
    background: #fef2f2;
    border: 1px solid #fee2e2;
    border-radius: 20px;

    font-size: 36px;
    font-weight: 700;
}

.request-error__title {
    margin: 0 0 10px;

    color: #151922;
    font-size: 24px;
    font-weight: 650;
}

.request-error__text {
    max-width: 430px;
    margin: 0 0 26px;

    color: #687181;
    font-size: 15px;
    line-height: 1.6;
}

@media (max-width: 700px) {
    .request-error {
        width: calc(100% - 24px);
        min-height: 360px;
        margin: 12px;
        padding: 35px 20px;
    }

    .request-error__title {
        font-size: 21px;
    }
}
</style>