<?php

declare(strict_types=1);

/**
 * Groq chat-capable models catalog.
 * - vision: can accept image input
 * - tier: cost/resource consumption indicator
 *   1 = cheap (small model, fast, low rate-limit usage)
 *   2 = mid (balanced)
 *   3 = premium (large, slow, high quality)
 *
 * Disemak dengan GET /models Groq pada 23 Okt 2026. Llama 3.1/3.3/4 dan Qwen 3 32B sudah DITUTUP Groq
 * (Mac–Ogos 2026) dan dibuang dari senarai. Admin juga menyemak senarai ini dengan Groq secara langsung
 * (cache 1 jam) dan menanda model yang tiada lagi — tambah model baharu di sini bila Groq melancarkannya.
 */
return [
    [
        'id' => 'openai/gpt-oss-20b',
        'name' => 'GPT-OSS 20B',
        'vision' => false,
        'tier' => 1,
        'note' => 'Pantas dan jimat kuota. Sesuai untuk soalan ringkas.',
    ],
    [
        'id' => 'openai/gpt-oss-120b',
        'name' => 'GPT-OSS 120B',
        'vision' => false,
        'tier' => 2,
        'note' => 'Lalai. Kualiti tinggi dan seimbang. Disyorkan untuk soalan teks.',
    ],
    [
        'id' => 'qwen/qwen3.8-27b',
        'name' => 'Qwen 3.8 27B',
        'vision' => true,
        'tier' => 2,
        'note' => 'Menerima gambar dan baik dalam BM. Lalai untuk soalan bergambar. Had output percuma kecil (kira-kira 1,000 token seminit setiap kunci).',
    ],
];
