<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            [
                'slug' => 'social-realism',
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Social Realism',
                        'description' => 'Portraying authentic social conditions and human struggles.',
                        'seo_title' => 'Social Realism Hindi Books',
                        'seo_description' => 'Explore Hindi social realism literature.',
                    ],
                    'hi' => [
                        'name' => 'सामाजिक यथार्थवाद',
                        'description' => 'ग्रामीण जीवन, सामाजिक विसंगतियों और मानवीय संघर्षों का सजीव चित्रण।',
                        'seo_title' => 'सामाजिक यथार्थवादी हिन्दी साहित्य',
                        'seo_description' => 'सामाजिक यथार्थवाद पर आधारित हिन्दी पुस्तकें।',
                    ],
                ],
            ],
            [
                'slug' => 'satire',
                'sort_order' => 2,
                'translations' => [
                    'en' => [
                        'name' => 'Satire',
                        'description' => 'Witty, insightful social and political commentary.',
                        'seo_title' => 'Hindi Satire Books PDF',
                        'seo_description' => 'Read acclaimed Hindi vyangya and satirical works.',
                    ],
                    'hi' => [
                        'name' => 'व्यंग्य',
                        'description' => 'सामाजिक एवं राजनीतिक विद्रूपताओं पर तीखा और विचारोत्तेजक कटाक्ष।',
                        'seo_title' => 'हिन्दी व्यंग्य पुस्तकें',
                        'seo_description' => 'महान व्यंग्यकारों की प्रसिद्ध पुस्तकें।',
                    ],
                ],
            ],
            [
                'slug' => 'chhayavad',
                'sort_order' => 3,
                'translations' => [
                    'en' => [
                        'name' => 'Chhayavad / Romanticism',
                        'description' => 'Lyrical depth, human emotion, and nature symbolism.',
                        'seo_title' => 'Chhayavadi Poetry & Literature',
                        'seo_description' => 'Hindi romantic and Chhayavadi era literary masterpieces.',
                    ],
                    'hi' => [
                        'name' => 'छायावाद',
                        'description' => 'आत्मभिव्यक्ति, प्रकृति-प्रेम और मानवीय संवेदनशीलता का उत्कृष्ट काव्य।',
                        'seo_title' => 'छायावादी काव्य एवं साहित्य',
                        'seo_description' => 'छायावाद के चार स्तंभों और प्रमुख रचनाकारों का काव्य संग्रह।',
                    ],
                ],
            ],
            [
                'slug' => 'drama',
                'sort_order' => 4,
                'translations' => [
                    'en' => [
                        'name' => 'Drama & Plays',
                        'description' => 'Theatrical scripts, historical reenactments, and dialogues.',
                        'seo_title' => 'Hindi Drama & Play Books PDF',
                        'seo_description' => 'Read famous Hindi theatrical plays and dramas.',
                    ],
                    'hi' => [
                        'name' => 'नाटक एवं रंगमंच',
                        'description' => 'रंगमंचीय आलेख, ऐतिहासिक नाटक और संवाद प्रधान कृतियाँ।',
                        'seo_title' => 'हिन्दी नाटक पुस्तकें पीडीएफ',
                        'seo_description' => 'प्रसिद्ध हिन्दी नाटकों का संकलन।',
                    ],
                ],
            ],
            [
                'slug' => 'philosophy',
                'sort_order' => 5,
                'translations' => [
                    'en' => [
                        'name' => 'Philosophy',
                        'description' => 'Inquiries into existence, consciousness, and human ethics.',
                        'seo_title' => 'Hindi Philosophy Books',
                        'seo_description' => 'Explore Indian and world philosophy in Hindi.',
                    ],
                    'hi' => [
                        'name' => 'दर्शन',
                        'description' => 'अस्तित्व, चेतना, नीति और जीवन के मूल प्रश्नों का अन्वेषण।',
                        'seo_title' => 'दार्शनिक चिंतन एवं पुस्तकें',
                        'seo_description' => 'भारतीय दर्शन और आचार विचार की पुस्तकें।',
                    ],
                ],
            ],
        ];

        foreach ($genres as $genreData) {
            $genre = Genre::updateOrCreate(
                ['slug' => $genreData['slug']],
                [
                    'sort_order' => $genreData['sort_order'],
                    'is_active' => true,
                ]
            );

            foreach ($genreData['translations'] as $locale => $trans) {
                $genre->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $trans
                );
            }
        }
    }
}
