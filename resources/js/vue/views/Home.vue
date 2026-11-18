
<template>
    <main class="max-w-7xl mx-auto px-6 py-16">
        <section class="text-center py-12">
            <p class="text-[#00FA9A] tracking-[0.3em] mb-4">WELCOME TO LIMENIGHT</p>

            <h1 class="text-5xl md:text-7xl font-bold mb-6">
                Играй. Отдыхай.
                <span class="text-[#B7FF00]">Побеждай.</span>
            </h1>

            <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-8">
                Твоё пространство для игр, отдыха и незабываемых впечатлений.
            </p>

            <router-link
                to="/app/zones"
                class="inline-block bg-[#B7FF00] text-black font-bold px-8 py-4 rounded-xl hover:bg-[#a5e600] transition"
            >
                Выбрать игровую зону
            </router-link>
        </section>

        <section class="mt-16">
            <h2 class="text-3xl font-bold mb-8">
                Наши <span class="text-[#B7FF00]">игровые зоны</span>
            </h2>

            <div class="grid md:grid-cols-3 gap-6">
                <article class="bg-[#101010] border border-[#292929] rounded-2xl p-8 hover:border-[#B7FF00] transition">
                    <p class="text-[#00FA9A] text-sm tracking-widest mb-4">Общий</p>
                    <h3 class="text-2xl font-bold mb-4">Общий зал</h3>
                    <p class="text-gray-400 mb-6">
                        Комфортное пространство для дружеских встреч и любимых игр.
                    </p>
                    <router-link to="/app/zones" class="text-[#B7FF00] font-bold">
                        Подробнее →
                    </router-link>
                </article>

                <article class="bg-[#101010] border border-[#292929] rounded-2xl p-8 hover:border-[#B7FF00] transition">
                    <p class="text-[#00FA9A] text-sm tracking-widest mb-4">VIP</p>
                    <h3 class="text-2xl font-bold mb-4">VIP-зона</h3>
                    <p class="text-gray-400 mb-6">
                        Уединённая атмосфера и комфорт для индивидуальной игры.
                    </p>
                    <router-link to="/app/zones" class="text-[#B7FF00] font-bold">
                        Подробнее →
                    </router-link>
                </article>

                <article class="bg-[#101010] border border-[#292929] rounded-2xl p-8 hover:border-[#B7FF00] transition">
                    <p class="text-[#00FA9A] text-sm tracking-widest mb-4">BOOTCAMP</p>
                    <h3 class="text-2xl font-bold mb-4">Буткемп</h3>
                    <p class="text-gray-400 mb-6">
                        Отдельное пространство для командных тренировок и соревнований.
                    </p>
                    <router-link to="/app/zones" class="text-[#B7FF00] font-bold">
                        Подробнее →
                    </router-link>
                </article>
            </div>
        </section>
    </main>
</template>
