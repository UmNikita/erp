<template>
    <div class="empty">
        <div>
            <h2>Клиенты не найдены</h2>
            <p>Измените поисковый запрос или выбранный фильтр.</p>
        </div>
    </div>
</template>

<style>
    .empty{
        display:flex;
        min-height:320px;
        align-items:center;
        justify-content:center;
        text-align:center
    }
    .empty h2{
        margin:0 0 8px;
        font-size:20px
    }
    .empty p{
        margin:0;
        color:var(--muted);
        font-size:14px
    }
</style>