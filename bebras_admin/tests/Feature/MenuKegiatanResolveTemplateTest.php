<?php

namespace Tests\Feature;

use App\Models\MenuKegiatan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuKegiatanResolveTemplateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * resolveTemplate() pada menu root dengan template 'workshop'
     * harus mengembalikan 'workshop'.
     * Requirements: 2.1
     */
    public function test_resolve_template_menu_root_dengan_template_workshop(): void
    {
        $menuRoot = MenuKegiatan::create([
            'nama_menu' => 'Workshop',
            'slug'      => 'workshop',
            'urutan'    => 1,
            'template'  => 'workshop',
        ]);

        $this->assertSame('workshop', $menuRoot->resolveTemplate());
    }

    /**
     * resolveTemplate() pada menu root dengan template null
     * harus mengembalikan null.
     * Requirements: 2.1
     */
    public function test_resolve_template_menu_root_dengan_template_null(): void
    {
        $menuRoot = MenuKegiatan::create([
            'nama_menu' => 'Kegiatan Umum',
            'slug'      => 'kegiatan-umum',
            'urutan'    => 1,
            'template'  => null,
        ]);

        $this->assertNull($menuRoot->resolveTemplate());
    }

    /**
     * resolveTemplate() pada menu child dengan parent ber-template 'bebras_challenge',
     * sudah eager-loaded, harus mengembalikan 'bebras_challenge'.
     * Requirements: 2.2
     */
    public function test_resolve_template_menu_child_eager_load_mengembalikan_template_parent(): void
    {
        $parent = MenuKegiatan::create([
            'nama_menu' => 'Bebras Challenge',
            'slug'      => 'bebras-challenge',
            'urutan'    => 1,
            'template'  => 'bebras_challenge',
        ]);

        $child = MenuKegiatan::create([
            'parent_id' => $parent->id,
            'nama_menu' => 'Bebras Challenge 2024',
            'slug'      => 'bebras-challenge-2024',
            'urutan'    => 1,
            'template'  => null,
        ]);

        // Eager-load relasi parent
        $childEager = MenuKegiatan::with('parent')->find($child->id);

        $this->assertSame('bebras_challenge', $childEager->resolveTemplate());
    }

    /**
     * resolveTemplate() pada menu child tanpa eager-load (lazy-load)
     * harus mengembalikan nilai yang identik dengan hasil eager-load.
     * Requirements: 2.3
     */
    public function test_resolve_template_menu_child_lazy_load_identik_dengan_eager_load(): void
    {
        $parent = MenuKegiatan::create([
            'nama_menu' => 'Pengumuman Hasil',
            'slug'      => 'pengumuman-hasil',
            'urutan'    => 1,
            'template'  => 'pengumuman_hasil',
        ]);

        $child = MenuKegiatan::create([
            'parent_id' => $parent->id,
            'nama_menu' => 'Pengumuman Hasil 2024',
            'slug'      => 'pengumuman-hasil-2024',
            'urutan'    => 1,
            'template'  => null,
        ]);

        // Eager-load
        $childEager = MenuKegiatan::with('parent')->find($child->id);
        $hasilEager = $childEager->resolveTemplate();

        // Lazy-load (tanpa with)
        $childLazy = MenuKegiatan::find($child->id);
        $hasilLazy = $childLazy->resolveTemplate();

        $this->assertSame($hasilEager, $hasilLazy);
        $this->assertSame('pengumuman_hasil', $hasilLazy);
    }
}
