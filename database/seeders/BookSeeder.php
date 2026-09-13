<?php

namespace Database\Seeders;

use App\Enums\AuthorRole;
use App\Enums\BookStatus;
use App\Enums\ReviewStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $hi = Language::where('code', 'hi')->first();
        $en = Language::where('code', 'en')->first();

        $premchand = Author::where('slug', 'munshi-premchand')->first();
        $prasad = Author::where('slug', 'jaishankar-prasad')->first();
        $varma = Author::where('slug', 'mahadevi-varma')->first();
        $bachchan = Author::where('slug', 'harivansh-rai-bachchan')->first();

        $literature = Category::where('slug', 'literature')->first();
        $fiction = Category::where('slug', 'fiction')->first();
        $poetry = Category::where('slug', 'poetry')->first();

        $socialRealism = Genre::where('slug', 'social-realism')->first();
        $chhayavad = Genre::where('slug', 'chhayavad')->first();
        $drama = Genre::where('slug', 'drama')->first();

        $testUser = User::first() ?? User::factory()->create([
            'name' => 'Hindi Reader',
            'email' => 'reader@example.com',
        ]);

        $books = [
            [
                'slug' => 'godaan',
                'status' => BookStatus::Published,
                'publication_year' => 1936,
                'publisher' => 'Saraswati Press',
                'isbn' => '9788170280019',
                'isbn13' => '9788170280019',
                'pages' => 312,
                'edition' => 'Centenary Edition',
                'original_language_id' => $hi?->id,
                'rating_average' => 4.85,
                'rating_count' => 142,
                'review_count' => 86,
                'view_count' => 12450,
                'source_url' => 'https://archive.org/details/godaan-premchand',
                'license_type' => 'Public Domain',
                'rights_holder' => 'Public Domain',
                'copyright_status' => 'public_domain',
                'permission_notes' => 'Author passed away in 1936. Work entered public domain.',
                'author' => $premchand,
                'categories' => array_filter([$literature?->id, $fiction?->id]),
                'genres' => array_filter([$socialRealism?->id]),
                'translations' => [
                    'en' => [
                        'title' => 'Godaan (The Gift of a Cow)',
                        'short_description' => 'Premchand’s magnum opus depicting peasant hardship, debt, and human resilience in rural India.',
                        'description' => 'Godaan revolves around Hori, an impoverished peasant farmer whose lifelong dream is to own a milking cow. Through his journey, Premchand delivers a masterful critique of feudal exploitation, caste rigidity, and societal inequities.',
                        'summary' => 'A timeless epic exploring agrarian hardship, structural injustice, and enduring human compassion in 1930s North India.',
                        'key_themes' => ['Feudal Exploitation', 'Peasant Debt', 'Social Dignity', 'Moral Resilience'],
                        'why_read' => 'Widely recognized as the greatest novel of modern Hindi literature and an essential chronicle of Indian social history.',
                        'seo_title' => 'Godaan by Munshi Premchand PDF Download - Hindi PDF Library',
                        'seo_description' => 'Read and download complete Godaan novel by Munshi Premchand in Hindi PDF format.',
                    ],
                    'hi' => [
                        'title' => 'गोदान',
                        'short_description' => 'भारतीय ग्रामीण समाज, कृषक जीवन के संघर्ष और अमर मानवीय गरिमा का कालजयी महाकाव्य।',
                        'description' => 'गोदान उपन्यास सम्राट मुंशी प्रेमचंद की सर्वोत्कृष्ट रचना है। यह होरी और धनिया के संघर्ष, महाजनी सभ्यता के शोषण और एक किसान के गाय रखने के सरल सपने की अमर गाथा है।',
                        'summary' => 'भारतीय किसान के अथाह संघर्ष, सामाजिक विसंगतियों और नैतिक मूल्यों का सजीव दस्तावेज।',
                        'key_themes' => ['कृषक जीवन', 'महाजनी शोषण', 'धार्मिक पाखंड', 'मानवीय करुणा'],
                        'why_read' => 'हिन्दी साहित्य का सर्वश्रेष्ठ उपन्यास, जो हर संवेदनशील पाठक को भारतीय यथार्थ से जोड़ता है।',
                        'seo_title' => 'गोदान - मुंशी प्रेमचंद उपन्यास पीडीएफ डाउनलोड',
                        'seo_description' => 'उपन्यास सम्राट प्रेमचंद का कालजयी उपन्यास गोदान संपूर्ण हिन्दी पीडीएफ में निःशुल्क पढ़ें।',
                    ],
                ],
            ],
            [
                'slug' => 'gaban',
                'status' => BookStatus::Published,
                'publication_year' => 1931,
                'publisher' => 'Saraswati Press',
                'isbn' => '9788170280026',
                'isbn13' => '9788170280026',
                'pages' => 264,
                'edition' => 'Standard Edition',
                'original_language_id' => $hi?->id,
                'rating_average' => 4.60,
                'rating_count' => 98,
                'review_count' => 45,
                'view_count' => 8400,
                'source_url' => 'https://archive.org/details/gaban-premchand',
                'license_type' => 'Public Domain',
                'rights_holder' => 'Public Domain',
                'copyright_status' => 'public_domain',
                'permission_notes' => 'Author passed away in 1936. Public domain.',
                'author' => $premchand,
                'categories' => array_filter([$literature?->id, $fiction?->id]),
                'genres' => array_filter([$socialRealism?->id]),
                'translations' => [
                    'en' => [
                        'title' => 'Gaban (Embezzlement)',
                        'short_description' => 'A moral and psychological drama exploring vanity, debt, and redemption in colonial urban India.',
                        'description' => 'Gaban depicts the tragic moral descent of Ramanath, whose pursuit of superficial luxury and jewels for his wife Jalpa leads him into debt and embezzlement.',
                        'summary' => 'A psychological narrative exploring societal pretensions and the path to moral awakening.',
                        'key_themes' => ['Middle-Class Illusion', 'Materialism', 'Moral Redemption'],
                        'why_read' => 'A gripping character study of human vulnerability and moral dilemmas.',
                        'seo_title' => 'Gaban by Premchand PDF Download',
                        'seo_description' => 'Download Gaban novel by Munshi Premchand in PDF format.',
                    ],
                    'hi' => [
                        'title' => 'गबन',
                        'short_description' => 'मध्यमवर्गीय मिथ्या प्रदर्शन, आभूषण-प्रेम और नैतिक पतन से पुनरुत्थान की प्रेरक कथा।',
                        'description' => 'रमानाथ के आभूषण-प्रेम और दिखावे की प्रवृत्ति से शुरू होकर आत्म-शोधन तक पहुँचने वाला प्रेमचंद का प्रसिद्ध उपन्यास।',
                        'summary' => 'सामाजिक आडंबर और मानवीय दुर्बलताओं पर गहरा मनोवैज्ञानिक प्रहार।',
                        'key_themes' => ['आडंबर', 'आभूषण-लालसा', 'प्रायश्चित'],
                        'why_read' => 'मध्यमवर्गीय मनोवृत्ति का अत्यंत प्रामाणिक और रोचक विश्लेषण।',
                        'seo_title' => 'गबन - मुंशी प्रेमचंद पीडीएफ पुस्तक',
                        'seo_description' => 'प्रेमचंद का प्रसिद्ध उपन्यास गबन हिन्दी में पीडीएफ डाउनलोड करें।',
                    ],
                ],
            ],
            [
                'slug' => 'kamayani',
                'status' => BookStatus::Published,
                'publication_year' => 1936,
                'publisher' => 'Bharati Bhandar',
                'isbn' => '9788170280033',
                'isbn13' => '9788170280033',
                'pages' => 280,
                'edition' => 'Deluxe Edition',
                'original_language_id' => $hi?->id,
                'rating_average' => 4.90,
                'rating_count' => 110,
                'review_count' => 60,
                'view_count' => 9700,
                'source_url' => 'https://archive.org/details/kamayani-prasad',
                'license_type' => 'Public Domain',
                'rights_holder' => 'Public Domain',
                'copyright_status' => 'public_domain',
                'permission_notes' => 'Author passed away in 1937. Public domain.',
                'author' => $prasad,
                'categories' => array_filter([$literature?->id, $poetry?->id]),
                'genres' => array_filter([$chhayavad?->id]),
                'translations' => [
                    'en' => [
                        'title' => 'Kamayani',
                        'short_description' => 'A monumental philosophical epic poem exploring human consciousness, emotion, and intellect.',
                        'description' => 'Kamayani stands as the zenith of Chhayavadi poetry, weaving allegorical figures Manu, Shraddha, and Ida to represent human will, emotion, and rational intellect.',
                        'summary' => 'A grand philosophical voyage through human evolution and inner balance.',
                        'key_themes' => ['Human Consciousness', 'Faith vs Reason', 'Harmony with Nature'],
                        'why_read' => 'Considered one of the greatest philosophical epics in 20th-century world literature.',
                        'seo_title' => 'Kamayani by Jaishankar Prasad PDF',
                        'seo_description' => 'Download Kamayani epic Hindi poetry book by Jaishankar Prasad in PDF.',
                    ],
                    'hi' => [
                        'title' => 'कामायनी',
                        'short_description' => 'छायावादी युग का कालजयी महाकाव्य, जिसमें मानव चेतना, श्रद्धा और बुद्धि का समन्वित दर्शन है।',
                        'description' => 'महाकवि जयशंकर प्रसाद की कामायनी हिन्दी साहित्य की अप्रतिम निधि है। मनु, श्रद्धा और इड़ा के प्रतीकों के माध्यम से जीवन के परम आनंद का निरूपण।',
                        'summary' => 'मनुष्य की आदिम व्यथा और आनंद की उपलब्धि की दार्शनिक काव्य यात्रा।',
                        'key_themes' => ['मनुष्य चेतना', 'समन्वय', 'आनंदवाद'],
                        'why_read' => 'आधुनिक हिन्दी महाकाव्यों का सिरमौर, जो काव्य सौंदर्य और गंभीर दर्शन का संगम है।',
                        'seo_title' => 'कामायनी महाकाव्य - जयशंकर प्रसाद पीडीएफ',
                        'seo_description' => 'कामायनी संपूर्ण महाकाव्य हिन्दी पीडीएफ में डाउनलोड करें और पढ़ें।',
                    ],
                ],
            ],
            [
                'slug' => 'madhushala',
                'status' => BookStatus::Published,
                'publication_year' => 1935,
                'publisher' => 'Rajpal & Sons',
                'isbn' => '9788170280040',
                'isbn13' => '9788170280040',
                'pages' => 140,
                'edition' => 'Pocket Edition',
                'original_language_id' => $hi?->id,
                'rating_average' => 4.95,
                'rating_count' => 220,
                'review_count' => 130,
                'view_count' => 18600,
                'source_url' => 'https://archive.org/details/madhushala-bachchan',
                'license_type' => 'Permitted Archival Edition',
                'rights_holder' => 'Cultural Heritage Preservation',
                'copyright_status' => 'public_domain',
                'permission_notes' => 'Widely published cultural classic.',
                'author' => $bachchan,
                'categories' => array_filter([$literature?->id, $poetry?->id]),
                'genres' => array_filter([$chhayavad?->id]),
                'translations' => [
                    'en' => [
                        'title' => 'Madhushala (The Tavern)',
                        'short_description' => 'An iconic 135-quatrain poem celebrating life, secular brotherhood, and spiritual musings.',
                        'description' => 'Through the rich metaphor of the tavern (Madhushala), wine (Madira), and the cup (Pyala), Bachchan delivers profound reflections on human mortality and universal equality.',
                        'summary' => 'A lyrical celebration of brotherhood and life beyond religious dogmatism.',
                        'key_themes' => ['Secular Fraternity', 'Philosophy of Life', 'Existential Beauty'],
                        'why_read' => 'A beloved masterpiece whose musical rhythms and universal philosophy have enchanted generations.',
                        'seo_title' => 'Madhushala by Harivansh Rai Bachchan PDF',
                        'seo_description' => 'Read Madhushala verses and poems in Hindi PDF format.',
                    ],
                    'hi' => [
                        'title' => 'मधुशाला',
                        'short_description' => '135 रुबाइयों का अमर काव्य संग्रह, जो जीवन, समरसता और मानव प्रेम का अनूठा गान है।',
                        'description' => 'हरिवंश राय बच्चन की सर्वाधिक लोकप्रिय कृति मधुशाला, जिसने धार्मिक और सामाजिक भेद को मिटाकर प्रेम और भ्रातृत्व का संदेश दिया।',
                        'summary' => 'जीवन के रहस्यों, सुख-दुख और मानवीय एकता का अद्भुत काव्यात्मक उत्सव।',
                        'key_themes' => ['साम्प्रदायिक सद्भाव', 'जीवन-दर्शन', 'मानव प्रेम'],
                        'why_read' => 'हिन्दी कविता की सर्वाधिक लोकप्रिय और कंठहार बनने वाली अमर कृति।',
                        'seo_title' => 'मधुशाला - हरिवंश राय बच्चन पीडीएफ डाउनलोड',
                        'seo_description' => 'हरिवंश राय बच्चन की कालजयी मधुशाला संपूर्ण हिन्दी पीडीएफ में उपलब्ध।',
                    ],
                ],
            ],
            [
                'slug' => 'yama',
                'status' => BookStatus::Published,
                'publication_year' => 1940,
                'publisher' => 'Kitabistan',
                'isbn' => '9788170280057',
                'isbn13' => '9788170280057',
                'pages' => 210,
                'edition' => 'Jnanpith Award Edition',
                'original_language_id' => $hi?->id,
                'rating_average' => 4.75,
                'rating_count' => 84,
                'review_count' => 38,
                'view_count' => 6200,
                'source_url' => 'https://archive.org/details/yama-mahadevi',
                'license_type' => 'Public Domain',
                'rights_holder' => 'Public Domain',
                'copyright_status' => 'public_domain',
                'permission_notes' => 'Cultural landmark publication.',
                'author' => $varma,
                'categories' => array_filter([$literature?->id, $poetry?->id]),
                'genres' => array_filter([$chhayavad?->id]),
                'translations' => [
                    'en' => [
                        'title' => 'Yama',
                        'short_description' => 'Jnanpith Award-winning anthology bringing together Mahadevi Varma’s finest poetic creations.',
                        'description' => 'Yama unites four celebrated poetic collections: Nihar, Rashmi, Neeraja, and Sandhyageet, capturing the depths of mystical longing and nature symbolism.',
                        'summary' => 'An exquisite anthology of spiritual yearning and refined lyricism.',
                        'key_themes' => ['Mysticism', 'Spiritual Longing', 'Emotional Depth'],
                        'why_read' => 'Conferred the prestigious Jnanpith Award; represents modern Hindi poetry at its aesthetic summit.',
                        'seo_title' => 'Yama by Mahadevi Varma PDF Download',
                        'seo_description' => 'Download Yama poetry collection by Mahadevi Varma in PDF format.',
                    ],
                    'hi' => [
                        'title' => 'यामा',
                        'short_description' => 'ज्ञानपीठ पुरस्कार से सम्मानित महादेवी वर्मा का कालजयी काव्य संकलन।',
                        'description' => 'नीहार, रश्मि, नीरजा और सांध्यगीत का ऐतिहासिक संकलन, जो छायावादी रहस्यवाद और विरह-वेदना का शिखर है।',
                        'summary' => 'रहस्यवादी चेतना और सूक्ष्म मानवीय संवेदनों का अमर काव्य संग्रह।',
                        'key_themes' => ['रहस्यवाद', 'प्रकृति-प्रेम', 'आत्म-समर्पण'],
                        'why_read' => 'भारतीय साहित्य के सर्वोच्च सम्मान ज्ञानपीठ से अलंकृत अप्रतिम काव्य ग्रन्थ।',
                        'seo_title' => 'यामा - महादेवी वर्मा काव्य संग्रह पीडीएफ',
                        'seo_description' => 'महादेवी वर्मा की प्रसिद्ध कृति यामा हिन्दी पीडीएफ में पढ़ें और डाउनलोड करें।',
                    ],
                ],
            ],
            [
                'slug' => 'nirmala',
                'status' => BookStatus::Published,
                'publication_year' => 1927,
                'publisher' => 'Chand Press',
                'isbn' => '9788170280064',
                'isbn13' => '9788170280064',
                'pages' => 220,
                'edition' => 'Centenary Edition',
                'original_language_id' => $hi?->id,
                'rating_average' => 4.80,
                'rating_count' => 105,
                'review_count' => 52,
                'view_count' => 7900,
                'source_url' => 'https://archive.org/details/nirmala-premchand',
                'license_type' => 'Public Domain',
                'rights_holder' => 'Public Domain',
                'copyright_status' => 'public_domain',
                'permission_notes' => 'Author passed away in 1936. Public domain.',
                'author' => $premchand,
                'categories' => array_filter([$literature?->id, $fiction?->id]),
                'genres' => array_filter([$socialRealism?->id, $drama?->id]),
                'translations' => [
                    'en' => [
                        'title' => 'Nirmala',
                        'short_description' => 'A searing social critique targeting the dowry system and ill-matched marriages.',
                        'description' => 'Nirmala chronicles the tragic fate of a young woman married to a widower older than her father, presenting a heartbreaking indictment of societal greed and female subjugation.',
                        'summary' => 'A powerful realistic critique of social customs and the dowry scourge in early 20th-century India.',
                        'key_themes' => ['Dowry Tragedy', 'Female Subjugation', 'Social Reform'],
                        'why_read' => 'A foundational social novel that catalyzed discussions on women’s rights in India.',
                        'seo_title' => 'Nirmala by Munshi Premchand PDF Download',
                        'seo_description' => 'Download Nirmala Hindi novel by Premchand in PDF format.',
                    ],
                    'hi' => [
                        'title' => 'निर्मला',
                        'short_description' => 'दहेज प्रथा और अनमेल विवाह की त्रासदी पर प्रेमचंद का मार्मिक और संवेदनशील उपन्यास।',
                        'description' => 'एक युवती निर्मला के अनमेल विवाह और उसके परिणामस्वरूप परिवार के विघटन की हृदयस्पर्शी कथा, जो सामाजिक कुरीतियों को झकझोरती है।',
                        'summary' => 'दहेज की वेदी पर बलि चढ़ी भारतीय नारी की कारुणिक गाथा।',
                        'key_themes' => ['दहेज प्रथा', 'अनमेल विवाह', 'नारी अस्मिता'],
                        'why_read' => 'सामाजिक सुधार और स्त्री-संवेदना पर लिखा गया एक अत्यंत प्रभावशाली उपन्यास।',
                        'seo_title' => 'निर्मला - मुंशी प्रेमचंद उपन्यास पीडीएफ',
                        'seo_description' => 'प्रेमचंद का प्रसिद्ध सामाजिक उपन्यास निर्मला हिन्दी पीडीएफ में निःशुल्क पढ़ें।',
                    ],
                ],
            ],
        ];

        foreach ($books as $bookData) {
            $book = Book::updateOrCreate(
                ['slug' => $bookData['slug']],
                [
                    'status' => $bookData['status'],
                    'publication_year' => $bookData['publication_year'],
                    'publisher' => $bookData['publisher'],
                    'isbn' => $bookData['isbn'],
                    'isbn13' => $bookData['isbn13'],
                    'pages' => $bookData['pages'],
                    'edition' => $bookData['edition'],
                    'original_language_id' => $bookData['original_language_id'],
                    'rating_average' => $bookData['rating_average'],
                    'rating_count' => $bookData['rating_count'],
                    'review_count' => $bookData['review_count'],
                    'view_count' => $bookData['view_count'],
                    'source_url' => $bookData['source_url'],
                    'license_type' => $bookData['license_type'],
                    'rights_holder' => $bookData['rights_holder'],
                    'copyright_status' => $bookData['copyright_status'],
                    'permission_notes' => $bookData['permission_notes'],
                ]
            );

            // Seed translations
            foreach ($bookData['translations'] as $locale => $trans) {
                $book->translations()->updateOrCreate(
                    ['locale' => $locale],
                    $trans
                );
            }

            // Sync author
            if ($bookData['author']) {
                $book->authors()->syncWithoutDetaching([
                    $bookData['author']->id => ['role' => AuthorRole::Author->value],
                ]);
            }

            // Sync categories & genres
            $book->categories()->sync($bookData['categories']);
            $book->genres()->sync($bookData['genres']);

            // Sync languages (Available in both Hindi and English)
            $book->languages()->sync(array_filter([$hi?->id, $en?->id]));

            // Seed sample review and favorite for test user
            Review::updateOrCreate(
                ['user_id' => $testUser->id, 'book_id' => $book->id],
                [
                    'rating' => 5,
                    'title' => 'A true literary treasure',
                    'body' => 'Deeply moving and beautifully written. A masterpiece of Indian literature.',
                    'status' => ReviewStatus::Approved,
                    'is_verified' => true,
                    'ip_hash' => hash('sha256', '127.0.0.1'),
                    'user_agent_hash' => hash('sha256', 'Mozilla/5.0'),
                ]
            );

            Favorite::firstOrCreate([
                'user_id' => $testUser->id,
                'book_id' => $book->id,
            ]);
        }
    }
}
