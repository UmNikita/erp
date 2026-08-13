<template>
    <div class="archive-page__header">
        <div>
            <h1 class="archive-page__title">Архив сделок</h1>
            <p class="archive-page__description">Сделки, которые были перемещены в архив</p>
        </div>
    </div>

    <div class="archive-page__toolbar">
        <div class="archive-page__search">
            <svg viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                <path d="M16.2 16.2L21 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>

            <input
                type="text"
                placeholder="Поиск по названию сделки, клиенту или компании..."
            >
        </div>

        <button class="archive-page__filter" type="button">
            Воронка
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <button class="archive-page__filter" type="button">
            Менеджер
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
</template>


<style scoped>
    .archive-page__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 28px;
    }
    .archive-page__title {
        margin: 0 0 7px;
        color: #171b24;
        font-size: 29px;
        font-weight: 700;
        line-height: 1.2;
    }

    .archive-page__description {
        margin: 0;
        color: #758097;
        font-size: 14px;
    }

    .archive-page__toolbar {
        display: grid;
        grid-template-columns: minmax(320px, 1fr) auto auto;
        gap: 10px;
        margin-bottom: 18px;
    }
    
    .archive-page__search {
        position: relative;
    }

    .archive-page__search svg {
        position: absolute;
        top: 50%;
        left: 15px;
        width: 19px;
        height: 19px;
        transform: translateY(-50%);
        color: #7c8698;
    }

    .archive-page__search input {
        width: 100%;
        height: 46px;
        padding: 0 15px 0 45px;
        color: #171b24;
        background: #ffffff;
        border: 1px solid #dce1e8;
        border-radius: 10px;
        outline: none;
        font: inherit;
        font-size: 13px;
    }

    .archive-page__search input:focus {
        border-color: #1267f4;
        box-shadow: 0 0 0 3px rgba(18, 103, 244, 0.08);
    }

    .archive-page__filter {
        min-width: 130px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 0 14px;
        color: #3d4656;
        background: #ffffff;
        border: 1px solid #dce1e8;
        border-radius: 10px;
        font: inherit;
        font-size: 13px;
        font-weight: 500;

        cursor: pointer;
    }

    .archive-page__filter:hover {
        background: #f8f9fb;
    }

    .archive-page__filter svg {
        width: 17px;
        height: 17px;
    }

    @media (max-width: 800px) {
        .archive-page__toolbar {
            grid-template-columns: 1fr;
        }
        .archive-page__filter {
            width: 100%;
        }
    }
</style>