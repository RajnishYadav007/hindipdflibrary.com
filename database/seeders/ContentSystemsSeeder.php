<?php

namespace Database\Seeders;

use App\Models\Audiobook;
use App\Models\Category;
use App\Models\DevotionalContent;
use App\Models\Language;
use App\Models\StorySeries;
use Illuminate\Database\Seeder;

class ContentSystemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hindi = Language::where('code', 'hi')->first() ?? Language::first();
        $english = Language::where('code', 'en')->first() ?? Language::first();

        // 1. Seed Audiobook: Godan (गोदान)
        $audiobook = Audiobook::firstOrCreate(
            ['slug' => 'godan-audiobook'],
            [
                'language_id' => $hindi?->id,
                'narrator' => 'समीर गोस्वामी (Sameer Goswami)',
                'duration_seconds' => 7200,
                'status' => 'published',
                'visibility' => 'public',
                'submission_type' => 'editorial',
                'published_at' => now(),
                'copyright_status' => 'Public Domain',
                'license_type' => 'CC0 / Public Domain',
                'is_featured' => true,
                'is_free' => true,
                'sort_order' => 1,
            ]
        );

        $audiobook->translations()->updateOrCreate(
            ['locale' => 'hi'],
            [
                'title' => 'गोदान (सम्पूर्ण ऑडियोबुक)',
                'description' => 'मुंशी प्रेमचंद द्वारा रचित कालजयी उपन्यास ‘गोदान’ का आधिकारिक एवं भावपूर्ण ऑडियो वाचन। ग्रामीण भारत की पीड़ा, होरी की व्यथा और किसान जीवन का अमर दस्तावेज।',
                'seo_title' => 'गोदान ऑडियोबुक — मुंशी प्रेमचंद | Godan Audiobook Hindi',
                'seo_description' => 'मुंशी प्रेमचंद का अमर उपन्यास गोदान अब ऑडियो प्रारूप में सुनें। सभी अध्याय निःशुल्क ऑनलाइन उपलब्ध।',
            ]
        );

        $audiobook->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'title' => 'Godan (Full Hindi Audiobook)',
                'description' => 'Complete audio narration of Munshi Premchand\'s masterpiece Godan.',
                'seo_title' => 'Godan Full Hindi Audiobook by Munshi Premchand',
                'seo_description' => 'Listen to the full Godan audiobook in Hindi online for free.',
            ]
        );

        // Chapters for Godan
        $chaptersData = [
            ['num' => 1, 'title' => 'अध्याय 1: होरी और भोला की भेंट', 'duration' => 1800],
            ['num' => 2, 'title' => 'अध्याय 2: पचिया और धनिया का संवाद', 'duration' => 2400],
            ['num' => 3, 'title' => 'अध्याय 3: गोबर का शहर प्रस्थान', 'duration' => 3000],
        ];

        foreach ($chaptersData as $ch) {
            $chapter = $audiobook->chapters()->updateOrCreate(
                ['chapter_number' => $ch['num']],
                [
                    'status' => 'published',
                    'duration_seconds' => $ch['duration'],
                    'sort_order' => $ch['num'],
                ]
            );

            $chapter->translations()->updateOrCreate(
                ['locale' => 'hi'],
                ['title' => $ch['title']]
            );
        }

        // 1.1. Seed Verified Multi-Chapter Audiobook Fixture: Gentlemen Prefer Blondes (LibriVox / Archive.org)
        $gpb = Audiobook::firstOrCreate(
            ['slug' => 'gentlemen-prefer-blondes-FHnFz'],
            [
                'language_id' => $english?->id,
                'author_name' => 'Anita Loos',
                'narrator' => 'LibriVox Volunteers',
                'duration_seconds' => 12840,
                'status' => 'published',
                'visibility' => 'public',
                'submission_type' => 'editorial',
                'published_at' => now(),
                'source_type' => 'archive_org',
                'source_url' => 'https://archive.org/details/gentlemenpreferblondes_2201_librivox',
                'external_audio_url' => 'https://archive.org/download/gentlemenpreferblondes_2201_librivox/gentlemenpreferblondes_01_loos_128kb.mp3',
                'copyright_status' => 'Public Domain',
                'license_type' => 'Public Domain / LibriVox Free Audio',
                'is_featured' => true,
                'is_free' => true,
                'sort_order' => 2,
            ]
        );

        $gpb->translations()->updateOrCreate(
            ['locale' => 'en'],
            [
                'title' => 'Gentlemen Prefer Blondes',
                'description' => 'The illuminating diary of a professional lady by Anita Loos, narrated in 9 chapters from LibriVox.',
                'seo_title' => 'Gentlemen Prefer Blondes Audiobook — Anita Loos | Full Audio',
                'seo_description' => 'Listen to the full unabridged audiobook of Gentlemen Prefer Blondes in 9 complete chapters for free online.',
            ]
        );

        $gpb->translations()->updateOrCreate(
            ['locale' => 'hi'],
            [
                'title' => 'जेंटलमैन प्रेफर ब्लॉन्ड्स (Gentlemen Prefer Blondes)',
                'description' => 'अनीता लूस का प्रसिद्ध उपन्यास, 9 अध्यायों में सम्पूर्ण ऑडियोबुक वाचन।',
                'seo_title' => 'जेंटलमैन प्रेफर ब्लॉन्ड्स ऑडियो उपन्यास',
                'seo_description' => 'अनीता लूस द्वारा रचित विश्वविख्यात उपन्यास का सम्पूर्ण ऑडियो वाचन निःशुल्क सुनें।',
            ]
        );

        $gpbChapters = [
            ['num' => 1, 'file' => 'gentlemenpreferblondes_01_loos_128kb.mp3', 'title' => 'Chapter 1: Gentlemen Prefer Blondes', 'dur' => 1751],
            ['num' => 2, 'file' => 'gentlemenpreferblondes_02_loos_128kb.mp3', 'title' => 'Chapter 2: Fate Keeps On Happening', 'dur' => 1363],
            ['num' => 3, 'file' => 'gentlemenpreferblondes_03_loos_128kb.mp3', 'title' => 'Chapter 3: London is Really Nothing', 'dur' => 1713],
            ['num' => 4, 'file' => 'gentlemenpreferblondes_04_loos_128kb.mp3', 'title' => 'Chapter 4: Paris is Devine, Part 1', 'dur' => 1382],
            ['num' => 5, 'file' => 'gentlemenpreferblondes_05_loos_128kb.mp3', 'title' => 'Chapter 5: Paris is Devine, Part 2', 'dur' => 988],
            ['num' => 6, 'file' => 'gentlemenpreferblondes_06_loos_128kb.mp3', 'title' => 'Chapter 6: The Central of Europe, Part 1', 'dur' => 1336],
            ['num' => 7, 'file' => 'gentlemenpreferblondes_07_loos_128kb.mp3', 'title' => 'Chapter 7: The Central of Europe, Part 2', 'dur' => 1379],
            ['num' => 8, 'file' => 'gentlemenpreferblondes_08_loos_128kb.mp3', 'title' => 'Chapter 8: Brains are Really Everything, Part 1', 'dur' => 1521],
            ['num' => 9, 'file' => 'gentlemenpreferblondes_09_loos_128kb.mp3', 'title' => 'Chapter 9: Brains are Really Everything, Part 2', 'dur' => 1407],
        ];

        foreach ($gpbChapters as $c) {
            $chapter = $gpb->chapters()->updateOrCreate(
                ['chapter_number' => $c['num']],
                [
                    'status' => 'published',
                    'duration_seconds' => $c['dur'],
                    'external_audio_url' => "https://archive.org/download/gentlemenpreferblondes_2201_librivox/{$c['file']}",
                    'source_file_name' => $c['file'],
                    'mime_type' => 'audio/mpeg',
                    'sort_order' => $c['num'],
                ]
            );

            $chapter->translations()->updateOrCreate(
                ['locale' => 'en'],
                ['title' => $c['title']]
            );

            $chapter->translations()->updateOrCreate(
                ['locale' => 'hi'],
                ['title' => str_replace('Chapter', 'अध्याय', $c['title'])]
            );
        }

        // 2. Seed Chalisa: Shree Hanuman Chalisa
        $chalisa = DevotionalContent::firstOrCreate(
            ['slug' => 'hanuman-chalisa-stotra'],
            [
                'type' => 'chalisa',
                'language_id' => $hindi?->id,
                'deity_name' => 'हनुमान',
                'author_name' => 'गोस्वामी तुलसीदास',
                'status' => 'published',
                'published_at' => now(),
                'copyright_status' => 'Public Domain',
                'sort_order' => 1,
            ]
        );

        $chalisaContent = <<<'TEXT'
॥ दोहा ॥
श्रीगुरु चरन सरोज रज, निज मनु मुकुरु सुधारि।
बरनऊं रघुबर बिमल जसु, जो दायकु फल चारि॥

बुद्धिहीन तनु जानिके, सुमिरौं पवन-कुमार।
बल बुद्धि बिद्या देहु मोहिं, हरहु कलेस बिकार॥

॥ चौपाई ॥
जय हनुमान ज्ञान गुन सागर। जय कपीस तिहुं लोक उजागर॥
राम दूत अतुलित बल धामा। अंजनि-पुत्र पवनसुत नामा॥
महाबीर बिक्रम बजरंगी। कुमति निवार सुमति के संगी॥
कंचन बरन बिराज सुबेसा। कानन कुंडल कुंचित केसा॥
हाथ बज्र औ ध्वजा बिराजै। कांधे मूंज जनेऊ साजै॥
संकर सुवन केसरीनंदन। तेज प्रताप महा जग बन्दन॥
विद्यावान गुनी अति चातुर। राम काज करिबे को आतुर॥
प्रभु चरित्र सुनिबे को रसिया। राम लखन सीता मन बसिया॥
सूक्ष्म रूप धरि सियहिं दिखावा। बिकट रूप धरि लंक जरावा॥
भीम रूप धरि असुर संहारे। रामचंद्र के काज संवारे॥
लाय सजीवन लखन जियाये। श्रीरघुबीर हरषि उर लाये॥
रघुपति कीन्ही बहुत बड़ाई। तुम मम प्रिय भरतहि सम भाई॥
सहस बदन तुम्हरो जस गावैं। अस कहि श्रीपति कंठ लगावैं॥
सनकादिक ब्रह्मादि मुनीसा। नारद सारद सहित अहीसा॥
जम कुबेर दिगपाल जहां ते। कबि कोबिद कहि सके कहां ते॥
तुम उपकार सुग्रीवहिं कीन्हा। राम मिलाय राज पद दीन्हा॥
तुम्हरो मंत्र बिभीषन माना। लंकेस्वर भए सब जग जाना॥
जुग सहस्र जोजन पर भानू। लील्यो ताहि मधुर फल जानू॥
प्रभु मुद्रिका मेलि मुख माहीं। जलधि लांघि गये अचरज नाहीं॥
दुर्गम काज जगत के जेते। सुगम अनुग्रह तुम्हरे तेते॥
राम दुआरे तुम रखवारे। होत न आज्ञा बिनु पैसारे॥
सब सुख लहै तुम्हारी सरना। तुम रक्षक काहू को डर ना॥
आपन तेज सम्हारो आपै। तीनों लोक हांक तें कांपै॥
भूत पिसाच निकट नहिं आवै। महाबीर जब नाम सुनावै॥
नासै रोग हरै सब पीरा। जपत निरंतर हनुमत बीरा॥
संकट तें हनुमान छुड़ावै। मन क्रम बचन ध्यान जो लावै॥
सब पर राम तपस्वी राजा। तिन के काज सकल तुम साजा॥
और मनोरथ जो कोई लावै। सोइ अमित जीवन फल पावै॥
चारों जुग परताप तुम्हारा। है परसिद्ध जगत उजियारा॥
साधु-संत के तुम रखवारे। असुर निकंदन राम दुलारे॥
अष्ट सिद्धि नौ निधि के दाता। अस बर दीन जानकी माता॥
राम रसायन तुम्हरे पासा। सदा रहो रघुपति के दासा॥
तुम्हरे भजन राम को पावै। जनम-जनम के दुख बिसरावै॥
अन्तकाल रघुबर पुर जाई। जहां जन्म हरि-भक्त कहाई॥
और देवता चित्त न धरई। हनुमत सेइ सर्ब सुख करई॥
संकट कटै मिटै सब पीरा। जो सुमिरै हनुमत बलबीरा॥
जै जै जै हनुमान गोसाईं। कृपा करहु गुरुदेव की नाईं॥
जो सत बार पाठ कर कोई। छूटहि बंदि महा सुख होई॥
जो यह पढ़ै हनुमान चालीसा। होय सिद्ध साखी गौरीसा॥
तुलसीदास सदा हरि चेरा। कीजै नाथ हृदय मंह डेरा॥

॥ दोहा ॥
पवनतनय संकट हरन, मंगल मूरति रूप।
राम लखन सीता सहित, हृदय बसहु सुर भूप॥
TEXT;

        $chalisa->translations()->updateOrCreate(
            ['locale' => 'hi'],
            [
                'title' => 'श्री हनुमान चालीसा (Shree Hanuman Chalisa)',
                'description' => 'गोस्वामी तुलसीदास कृत सम्पूर्ण श्री हनुमान चालीसा, दोहा व चौपाइयों सहित शुद्ध पाठ एवं ऑडियो श्रवण।',
                'content' => $chalisaContent,
                'seo_title' => 'श्री हनुमान चालीसा — शुद्ध पाठ एवं ऑडियो | Hanuman Chalisa in Hindi',
                'seo_description' => 'सम्पूर्ण श्री हनुमान चालीसा शुद्ध दोहा व चौपाई सहित पढ़ें एवं निःशुल्क ऑडियो सुनें।',
            ]
        );

        // 3. Seed Aarti: Hanuman Ji Ki Aarti
        $aarti = DevotionalContent::firstOrCreate(
            ['slug' => 'hanuman-ji-ki-aarti-stotra'],
            [
                'type' => 'aarti',
                'language_id' => $hindi?->id,
                'deity_name' => 'हनुमान',
                'author_name' => 'पारम्परिक सन्त साहित्य',
                'status' => 'published',
                'published_at' => now(),
                'copyright_status' => 'Public Domain',
                'sort_order' => 2,
            ]
        );

        $aartiContent = <<<'TEXT'
आरती कीजै हनुमान लला की।
दुष्ट दलन रघुनाथ कला की॥

जाके बल से गिरिवर कांपै।
रोग दोष जाके निकट न झांपै॥
अंजनि पुत्र महा बलदाई।
सन्तन के प्रभु सदा सहाई॥
आरती कीजै हनुमान लला की...

दे बीड़ा रघुनाथ पठाए।
लंका जारि सिया सुधि लाये॥
लंका सो कोट समुद्र सी खाई।
जात पवनसुत बार न लाई॥
आरती कीजै हनुमान लला की...

लंका जारि असुर संहारे।
सियारामजी के काज संवारे॥
लक्ष्मण मूर्छित पड़े सकारे।
आनि संजीवन प्रान उबारे॥
आरती कीजै हनुमान लला की...

पैठि पताल तोरि जम-कारे।
अहिरावण की भुजा उखारे॥
बाएं भुजा असुर दल मारे।
दाहिने भुजा संतजन तारे॥
आरती कीजै हनुमान लला की...

सुर नर मुनि आरती उतारें।
जय जय जय हनुमान उचारें॥
कंचन थार कपूर लौ छाई।
आरती करत संजना माई॥
आरती कीजै हनुमान लला की...

जो हनुमानजी की आरती गावै।
बसि बैकुंठ परम पद पावै॥
आरती कीजै हनुमान लला की।
दुष्ट दलन रघुनाथ कला की॥
TEXT;

        $aarti->translations()->updateOrCreate(
            ['locale' => 'hi'],
            [
                'title' => 'हनुमान जी की आरती (आरती कीजै हनुमान लला की)',
                'description' => 'श्री हनुमान जी की पावन आरती ‘आरती कीजै हनुमान लला की, दुष्ट दलन रघुनाथ कला की’ लिरिक्स व ऑडियो सहित।',
                'content' => $aartiContent,
                'seo_title' => 'हनुमान जी की आरती — आरती कीजै हनुमान लला की लिरिक्स व ऑडियो',
                'seo_description' => 'श्री हनुमान जी की प्रसिद्ध आरती शुद्ध पाठ व ऑडियो श्रवण के साथ पढ़ें।',
            ]
        );

        // 4. Seed Story Series: Panchatantra Stories
        $category = Category::first();

        $series = StorySeries::firstOrCreate(
            ['slug' => 'panchatantra-stories'],
            [
                'category_id' => $category?->id,
                'language_id' => $hindi?->id,
                'age_group' => '6-8',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
                'copyright_status' => 'Public Domain Folk Tale',
                'sort_order' => 1,
            ]
        );

        $series->translations()->updateOrCreate(
            ['locale' => 'hi'],
            [
                'title' => 'पंचतंत्र की अमर कहानियां (Panchatantra Stories)',
                'description' => 'पंडित विष्णु शर्मा द्वारा रचित बालकों के चरित्र निर्माण, बुद्धिमत्ता और नैतिक मूल्यों पर आधारित कालजयी प्रेरक पशु-पक्षी कथाएं।',
                'seo_title' => 'पंचतंत्र की बाल कहानियां — नैतिक व रोचक कथाएं | Panchatantra Stories in Hindi',
                'seo_description' => 'बच्चों के लिए पंचतंत्र की सर्वश्रेष्ठ प्रेरणादायक कहानियां, ऑडियो वाचन एवं आकर्षक प्रस्तुति के साथ।',
            ]
        );

        // Episode 1: शेर और चतुर खरगोश
        $ep1 = $series->episodes()->updateOrCreate(
            ['episode_number' => 1],
            [
                'narration_type' => 'tts',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $ep1Story = <<<'TEXT'
एक वन में भासुरक नाम का एक बहुत बलशाली और क्रूर शेर रहता था। वह प्रतिदिन अपनी भूख से कहीं अधिक पशुओं का शिकार कर डालता था। वन के सभी जीव-जंतु उसके भय से थर-थर कांपते थे।

एक दिन वन के समस्त प्रमुख पशु एकत्रित हुए और उन्होंने शेर के पास जाकर विनम्र प्रार्थना की, "महाराज! आप वन के राजा हैं। इस प्रकार बिना कारण इतने जीवों को मारने से शीघ्र ही यह वन सूना हो जाएगा। हम आपके भोजन के लिए प्रतिदिन बारी-बारी से स्वयं एक पशु आपके पास भेज दिया करेंगे।"

शेर ने उनकी बात मान ली और कहा, "यदि किसी दिन कोई पशु समय पर नहीं आया, तो मैं सबको मार डालूंगा।"

कुछ दिनों बाद एक चतुर नन्हें खरगोश की बारी आई। खरगोश ने मन ही मन सोचा कि मृत्यु तो निश्चित है, क्यों न कोई उपाय करके इस दुष्ट शेर से पूरे वन को मुक्ति दिलाई जाए। वह जानबूझकर बहुत धीमे-धीमे चला और मार्ग में दोपहर तक घूमता रहा।

मार्ग में उसे एक गहरा कुआं दिखा। कुएं में झांकने पर उसे अपना ही प्रतिबिंब दिखाई दिया। खरगोश की आंखों में एक युक्ति चमक उठी।

वह संध्या समय भूखे और क्रोध से गरजते हुए शेर के पास पहुंचा। शेर ने क्रोधित होकर दहाड़ लगाई, "अरे छोटे जीव! एक तो तू इतना तुच्छ है और ऊपर से इतनी देर से आया है! मैं अभी तुझे मारकर पूरे वन के पशुओं का नाश कर दूंगा।"

खरगोश ने हाथ जोड़कर कहा, "महाराज! इसमें मेरा कोई दोष नहीं है। हमारे साथ चार खरगोश और भेजे गए थे, परंतु मार्ग में एक अन्य बड़े शेर ने हमें रोक लिया और कहा कि वह इस वन का असली राजा है। मैं किसी प्रकार जान बचाकर आपको यह सूचना देने आया हूँ।"

भासुरक क्रोध से पागल हो उठा। उसने कहा, "मुझे तुरंत उस धृष्ट के पास ले चल! मैं अभी उसका अंत करता हूँ।"

खरगोश शेर को उसी गहरे कुएं के पास ले गया और बोला, "महाराज! वह दुष्ट इस भूमिगत दुर्ग में छिपा बैठा है।"

शेर ने कुएं में झांका। जल में उसे अपनी ही परछाईं दिखाई दी। उसने उसे ही दूसरा शेर समझकर जोर से गर्जना की। कुएं से गूंजती हुई अपनी ही दहाड़ सुनकर शेर और भड़क गया और उसने अपने शत्रु को समाप्त करने के लिए कुएं में छलांग लगा दी। कुएं के गहरे जल में डूबकर वह क्रूर शेर मर गया।

इस प्रकार चतुर खरगोश ने अपनी बुद्धि के बल पर स्वयं की और वन के समस्त प्राणियों की रक्षा की।

शिक्षा: बुद्धि बल से कहीं अधिक शक्तिशाली होती है। संकट के समय घबराने के स्थान पर सूझबूझ से काम लेना चाहिए।
TEXT;

        $ep1->translations()->updateOrCreate(
            ['locale' => 'hi'],
            [
                'title' => 'शेर और चतुर खरगोश (The Lion and the Clever Rabbit)',
                'slug' => 'the-lion-and-the-clever-rabbit',
                'content' => $ep1Story,
                'seo_title' => 'शेर और चतुर खरगोश — पंचतंत्र की बाल कहानी | Panchatantra Episode 1',
                'seo_description' => 'पंचतंत्र की प्रसिद्ध प्रेरक कहानी: शेर और चतुर खरगोश। बच्चों के लिए नैतिक शिक्षा और ऑडियो श्रवण।',
            ]
        );
    }
}
