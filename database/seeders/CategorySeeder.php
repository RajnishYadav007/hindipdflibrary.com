<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'literature',
                'sort_order' => 1,
                'parent_id' => null,
                'translations' => [
                    'en' => [
                        'name' => 'Literature',
                        'description' => 'Classic and modern literary compositions and timeless works.',
                        'seo_title' => 'Hindi Literature Books & PDF Download',
                        'seo_description' => 'Explore classic and contemporary Hindi literature books in digital PDF format.',
                    ],
                    'hi' => [
                        'name' => 'साहित्य',
                        'description' => 'शास्त्रीय एवं आधुनिक साहित्यिक कृतियाँ और कालजयी रचनाएं।',
                        'seo_title' => 'हिन्दी साहित्य पुस्तकें और पीडीएफ डाउनलोड',
                        'seo_description' => 'कालजयी हिन्दी साहित्य की उत्कृष्ट पुस्तकें पीडीएफ में पढ़ें और डाउनलोड करें।',
                    ],
                ],
                'children' => [
                    [
                        'slug' => 'fiction',
                        'sort_order' => 1,
                        'translations' => [
                            'en' => [
                                'name' => 'Fiction & Stories',
                                'description' => 'Short stories, novellas, and captivating novels.',
                                'seo_title' => 'Hindi Fiction Books - Free PDF',
                                'seo_description' => 'Read top Hindi fiction and short stories.',
                            ],
                            'hi' => [
                                'name' => 'कथा साहित्य',
                                'description' => 'कहानियां, लघु कथाएं और रोमांचक उपन्यास।',
                                'seo_title' => 'हिन्दी कथा साहित्य पुस्तकें',
                                'seo_description' => 'हिन्दी के प्रसिद्ध उपन्यास और कहानियों का संकलन।',
                            ],
                        ],
                    ],
                    [
                        'slug' => 'poetry',
                        'sort_order' => 2,
                        'translations' => [
                            'en' => [
                                'name' => 'Poetry',
                                'description' => 'Classic verses, ghazals, and modern poetic collections.',
                                'seo_title' => 'Hindi Poetry & Kavita Books PDF',
                                'seo_description' => 'Explore celebrated poems, dohas, and verses in Hindi.',
                            ],
                            'hi' => [
                                'name' => 'कविता एवं काव्य',
                                'description' => 'कालजयी कविताएं, गजलें, दोहे और आधुनिक काव्य संग्रह।',
                                'seo_title' => 'हिन्दी कविता पुस्तकें पीडीएफ',
                                'seo_description' => 'महान कवियों के काव्य संग्रह और कविताएं पढ़ें।',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'biography-history',
                'sort_order' => 2,
                'parent_id' => null,
                'translations' => [
                    'en' => [
                        'name' => 'Biography & History',
                        'description' => 'Life chronicles of historical icons, leaders, and thinkers.',
                        'seo_title' => 'Hindi Biographies and History Books PDF',
                        'seo_description' => 'Read authentic historical accounts and autobiographies in Hindi.',
                    ],
                    'hi' => [
                        'name' => 'जीवनी एवं इतिहास',
                        'description' => 'ऐतिहासिक महापुरुषों, विचारकों और स्वतंत्रता सेनानियों की गाथाएं।',
                        'seo_title' => 'जीवन चरित्र एवं ऐतिहासिक पुस्तकें हिन्दी में',
                        'seo_description' => 'ऐतिहासिक व्यक्तित्वों की प्रामाणिक जीवनियां और इतिहास की पुस्तकें।',
                    ],
                ],
            ],
            [
                'slug' => 'philosophy-spirituality',
                'sort_order' => 3,
                'parent_id' => null,
                'translations' => [
                    'en' => [
                        'name' => 'Philosophy & Wisdom',
                        'description' => 'Philosophical insights, ethics, and inspirational texts.',
                        'seo_title' => 'Hindi Philosophy & Wisdom Books',
                        'seo_description' => 'Ancient and contemporary philosophy books in Hindi.',
                    ],
                    'hi' => [
                        'name' => 'दर्शन एवं विचार',
                        'description' => 'दार्शनिक चिंतन, नीतिशास्त्र और प्रेरणादायक ग्रंथ।',
                        'seo_title' => 'दर्शन एवं विचार पुस्तकें पीडीएफ',
                        'seo_description' => 'भारतीय चिंतन एवं दर्शन की अमूल्य धरोहर पुस्तकें।',
                    ],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $category = Category::updateOrCreate(
                ['slug' => $catData['slug']],
                [
                    'sort_order' => $catData['sort_order'],
                    'parent_id' => $catData['parent_id'],
                    'is_active' => true,
                ]
            );

            foreach ($catData['translations'] as $locale => $trans) {
                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $trans
                );
            }

            if (isset($catData['children'])) {
                foreach ($catData['children'] as $childData) {
                    $child = Category::updateOrCreate(
                        ['slug' => $childData['slug']],
                        [
                            'sort_order' => $childData['sort_order'],
                            'parent_id' => $category->id,
                            'is_active' => true,
                        ]
                    );

                    foreach ($childData['translations'] as $cLocale => $cTrans) {
                        $child->translations()->updateOrCreate(
                            ['locale' => $cLocale],
                            $cTrans
                        );
                    }
                }
            }
        }
    }
}
