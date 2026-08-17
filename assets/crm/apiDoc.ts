enum HTTPMethod {
    POST="POST",
    GET="GET"
}
type Doc = {
    api: {
        route: string;
        method: HTTPMethod;
        title: string;
        desctiption: string;
        body: any | null;
        bodyExample: any | null;
    }[];
};

export const apiv1: Doc = {
    api: [
        {
            route: "/pipelines",
            method: HTTPMethod.GET,
            title: "Получения списка воронок и этапов",
            desctiption: "Возвращает список всех воронок и этапов с данными, включая id.",
            body: null,
            bodyExample: null
        },
        {
            route: "/lead",
            method: HTTPMethod.POST,
            title: "Создание сделки",
            desctiption: "Создание сделки. Обязательные поля: name и stage_id. Если передать email или phonne система сначала попытается найти клиента, если не найдет, то создаст его.",
            body: {
                name: "Название сделки",
                stage_id: "ID этапа воронки",
                budget: "Бюджет сделки",
                product: "Название продукта",
                source: "Источник сделки",
                next_action: "Следующее действие",
                date_next_action: "Дата следующего действия",
                comment: "Комментарий",
                client: {
                    name: "Название клиента",
                    phone: "Телефон клиента",
                    email: "Почта клиента",
                    channel: "Канал коммуникации"
                },
                responsible_id: 'ID ответственного пользователя'
            },
            bodyExample: {
                name: "РегионПлюс",
                stage_id: 1,
                budget: 75000,
                product: "CRM: лицензии + внедрение",
                source: "Интеграция",
                next_action: "Презентаци решения",
                date_next_action: "01.01.2026",
                comment: "Клиент рассматривает интеграцию в 1С",
                client: {
                    name: "ООО «ТехноПарк»",
                    phone: "84951234567",
                    email: "info@technopark.ru",
                    channel: "Email"
                },
                responsible_id: 1
            }
        }
    ]
}

export const baseURL = "https://hub.reklamynet.ru/public-api/v1";