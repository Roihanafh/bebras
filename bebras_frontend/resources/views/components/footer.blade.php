<footer class="relative bg-gradient-to-br from-blue-50 via-white to-green-50 text-gray-700 border-t pt-16 pb-10">
  <div class="max-w-7xl mx-auto px-6 lg:px-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">

      <!-- Logo & Deskripsi -->
      <div>
        <img src="{{ asset('img/bebras.png') }}" alt="Bebras Indonesia" class="h-14 mb-5 drop-shadow-md">
        <p class="text-sm leading-relaxed text-gray-600">
          🌟 Mengembangkan kemampuan <span class="font-semibold text-gray-900">computational thinking</span> siswa Indonesia sejak 2016.
        </p>
      </div>

      <!-- Kontak -->
      <div>
        <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
          📬 Kontak
        </h3>
        <ul class="space-y-3 text-sm">
          <li>
            <a href="mailto:mail@bebras.or.id" class="flex items-center gap-2 hover:text-blue-600 transition">
              ✉️ <span>mail@bebras.or.id</span>
            </a>
          </li>
          <li>
            <a href="http://bebras.or.id" target="_blank" class="flex items-center gap-2 hover:text-blue-600 transition">
              🔗 <span>bebras.or.id</span>
            </a>
          </li>
          <li class="text-gray-600">🌐 Situs web resmi Bebras Indonesia</li>
        </ul>
        <p class="mt-6 text-sm font-medium">🤝 Dikelola oleh:</p>
        <div class="flex items-center gap-4 mt-3">
          <a href="https://toki.id/" target="_blank" rel="noopener noreferrer" title="Tim Olimpiade Komputer Indonesia">
            <img src="{{ asset('/img/toki.png') }}" alt="TOKI" class="h-8 hover:scale-110 transition">
          </a>
          <img src="{{ asset('/img/maskot-bebras.png') }}" alt="Bebras Indonesia" class="h-8 hover:scale-110 transition">
        </div>
      </div>

      <!-- Tautan -->
      <div>
        <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
          🔗 Tautan
        </h3>
        <ul class="space-y-3 text-sm">
          <li><a href="https://www.bebras.org/" target="_blank" rel="noopener noreferrer" class="block hover:translate-x-1 transition">🌍 Bebras Internasional</a></li>
          <li><a href="https://gurupaudpnf.kemendikdasmen.go.id/artikel/berita/membangun-berpikir-komputasional-sejak-dini" target="_blank" rel="noopener noreferrer" class="block hover:translate-x-1 transition">🏛️ Kemendikdasmen</a></li>
          <li><a href="https://toki.id/" target="_blank" rel="noopener noreferrer" class="block hover:translate-x-1 transition">💻 TOKI Indonesia</a></li>
        </ul>
      </div>

      <!-- Arsip -->
      <div>
        <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900 mb-4">
          🗂️ Arsip
        </h3>
        <ul class="space-y-3 text-sm">
          <li>
            <a href="https://bebras.or.id/v3/workshop-2017/" class="flex items-center gap-2 hover:text-blue-600 transition">
              📘 <span>Workshop 2017</span>
            </a>
          </li>
          <li>
            <a href="https://bebras.or.id/v3/workshop/" class="flex items-center gap-2 hover:text-blue-600 transition">
              📗 <span>Workshop 2016</span>
            </a>
          </li>
          <li>
            <a href="https://www.bebras.org/?q=node/50" class="flex items-center gap-2 hover:text-blue-600 transition">
              📰 <span>Bebras News</span>
            </a>
          </li>
        </ul>
        <div class="mt-6 text-sm flex items-center gap-2">
          ⚡ <span>Olympia powered by</span>
          <img src="{{ asset('/img/gdp-logo.png') }}" alt="GDP Labs" class="h-7 hover:scale-110 transition">
        </div>
      </div>
    </div>

    <!-- Garis Divider -->
    <div class="border-t mt-12 pt-6 text-center text-sm text-gray-500">
      <p>© {{ date('Y') }} <span class="font-semibold">Bebras Indonesia</span>. Hak Cipta Dilindungi.</p>
      <p class="mt-2">Dibuat dengan ❤️ untuk pendidikan computational thinking di Indonesia.</p>
    </div>
  </div>
</footer>
