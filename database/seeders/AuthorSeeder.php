<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            [
                'slug' => 'munshi-premchand',
                'translations' => [
                    'en' => [
                        'name' => 'Munshi Premchand',
                        'biography' => 'Pioneering Hindi and Urdu novelist, acclaimed as Upanyas Samrat for his poignant realism and depiction of rural society.',
                        'seo_title' => 'Munshi Premchand Books PDF - Hindi PDF Library',
                        'seo_description' => 'Read and download famous novels and stories by Munshi Premchand in PDF format.',
                    ],
                    'hi' => [
                        'name' => 'मुंशी प्रेमचंद',
                        'biography' => 'हिन्दी और उर्दू के महान कथाकार एवं उपन्यास सम्राट, जिन्होंने सामाजिक यथार्थ और ग्रामीण जीवन का अमर चित्रण किया।',
                        'seo_title' => 'मुंशी प्रेमचंद की पुस्तकें और उपन्यास पीडीएफ',
                        'seo_description' => 'उपन्यास सम्राट मुंशी प्रेमचंद की कालजयी कृतियाँ और कहानियाँ निःशुल्क पढ़ें।',
                    ],
                ],
            ],
            [
                'slug' => 'jaishankar-prasad',
                'translations' => [
                    'en' => [
                        'name' => 'Jaishankar Prasad',
                        'biography' => 'Prominent figure of modern Hindi literature and one of the four pillars of the Chhayavad romanticist movement.',
                        'seo_title' => 'Jaishankar Prasad Books & Poems PDF',
                        'seo_description' => 'Discover Kamayani and other epic works of Jaishankar Prasad.',
                    ],
                    'hi' => [
                        'name' => 'जयशंकर प्रसाद',
                        'biography' => 'छायावादी युग के प्रवर्तक एवं महाकाव्य कामायनी के रचयिता, बहुमुखी नाटककार एवं कवि।',
                        'seo_title' => 'जयशंकर प्रसाद का साहित्य एवं कामायनी पीडीएफ',
                        'seo_description' => 'महाकवि जयशंकर प्रसाद के ऐतिहासिक नाटक और काव्य संग्रह।',
                    ],
                ],
            ],
            [
                'slug' => 'mahadevi-varma',
                'translations' => [
                    'en' => [
                        'name' => 'Mahadevi Varma',
                        'biography' => 'Esteemed Hindi poetess, essayist, and pioneer of the Chhayavad era, known as the modern Meera.',
                        'seo_title' => 'Mahadevi Varma Poems & Essays PDF',
                        'seo_description' => 'Read inspiring literary works and memoirs of Mahadevi Varma.',
                    ],
                    'hi' => [
                        'name' => 'महादेवी वर्मा',
                        'biography' => 'आधुनिक युग की मीरा कही जाने वाली छायावाद की प्रमुख कवयित्री, प्रखर गद्यकार और रेखाचित्र लेखिका।',
                        'seo_title' => 'महादेवी वर्मा की कविताएं और गद्य रचनाएं',
                        'seo_description' => 'महादेवी वर्मा के काव्य संग्रह, रेखाचित्र और संस्मरण पढ़ें।',
                    ],
                ],
            ],
            [
                'slug' => 'harivansh-rai-bachchan',
                'translations' => [
                    'en' => [
                        'name' => 'Harivansh Rai Bachchan',
                        'biography' => 'Renowned Hindi poet of the Nayi Kavita and Halaavaad era, most acclaimed for Madhushala.',
                        'seo_title' => 'Harivansh Rai Bachchan Poetry Books PDF',
                        'seo_description' => 'Explore the poetry and autobiography of Harivansh Rai Bachchan.',
                    ],
                    'hi' => [
                        'name' => 'हरिवंश राय बच्चन',
                        'biography' => 'हालावाद के अग्रदूत, मधुशाला के अमर रचयिता और हिन्दी के लोकप्रिय गीतकार एवं कवि।',
                        'seo_title' => 'हरिवंश राय बच्चन की मधुशाला और कविताएं',
                        'seo_description' => 'हरिवंश राय बच्चन के काव्य संग्रह और आत्मकथा पीडीएफ में उपलब्ध।',
                    ],
                ],
            ],
        ];

        foreach ($authors as $authorData) {
            $author = Author::updateOrCreate(
                ['slug' => $authorData['slug']],
                ['is_active' => true]
            );

            foreach ($authorData['translations'] as $locale => $trans) {
                $author->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $trans
                );
            }
        }
    }
}
