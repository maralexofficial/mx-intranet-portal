<?php

$json = file_get_contents(__DIR__ . '/sites.json');

if ($json === false) {
  die('Datei nicht gefunden!');
}

$sites = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
  die('JSON Fehler: ' . json_last_error_msg());
}

?>

<div x-data="statusApp(<?= htmlspecialchars(json_encode($sites), ENT_QUOTES, 'UTF-8') ?>)" x-init="init()"
  class="space-y-4">

  <h3 class="text-primary">Sites</h3>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <template x-for="site in sites" :key="site.title">
      <a :href="site.url" target="_blank"
        class="group bg-zinc-900 border border-zinc-800 rounded-2xl p-6 hover:border-orange-500 hover:shadow-lg hover:shadow-orange-500/10 transition-all duration-200">

        <!-- Header -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="text-2xl" x-text="site.icon"></span>

            <div>
              <h2 class="text-xl font-bold group-hover:text-orange-400 transition" x-text="site.title"></h2>
              <p class="text-gray-400 text-sm" x-text="site.subtitle"></p>
            </div>
          </div>

          <!-- STATUS -->
          <span :id="'status-' + getServiceId(site)" class="text-gray-500 text-xl transition-all duration-300">
            ●
          </span>
        </div>

        <!-- Content -->
        <div class="mt-4 border-t border-zinc-800 pt-4 text-sm text-gray-300 space-y-2">

          <p x-html="formatDescription(site.description)"></p>

          <template x-if="site.connectionAllowed && site.connectionAllowed.length">
            <ul class="text-orange-400 mt-2">
              <template x-for="range in site.connectionAllowed" :key="range">
                <li x-text="range"></li>
              </template>
            </ul>
          </template>

        </div>

      </a>
    </template>

    <!-- FALLBACK -->
    <template x-if="!sites.length">
      <div class="alert alert-primary col-span-2">
        No sites found!
      </div>
    </template>

  </div>
</div>