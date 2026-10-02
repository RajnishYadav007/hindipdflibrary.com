<?php

namespace Database\Seeders;

use App\Enums\BookStatus;
use App\Enums\CopyrightStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Database\Seeder;

class VedPuranSeeder extends Seeder
{
    public function run(): void
    {
        $hindi = Language::where('code', 'hi')->first();
        $english = Language::where('code', 'en')->first();

        // 1. Categories
        $vedPuranCat = Category::updateOrCreate(
            ['slug' => 'vedas-puranas'],
            ['sort_order' => 1, 'is_active' => true]
        );
        $vedPuranCat->translations()->updateOrCreate(['locale' => 'hi'], [
            'name' => 'वेद और पुराण',
            'description' => 'चारों पवित्र वेद, १८ महापुराण और उपनिषदों का प्रामाणिक डिजिटल संकलन।',
            'seo_title' => 'वेद और पुराण पीडीएफ डाउनलोड — चारों वेद एवं 18 महापुराण',
            'seo_description' => 'सम्पूर्ण 4 वेद (ऋग्वेद, यजुर्वेद, सामवेद, अथर्ववेद) और 18 महापुराण हिन्दी व संस्कृत में निःशुल्क पढ़ें व डाउनलोड करें।',
        ]);
        $vedPuranCat->translations()->updateOrCreate(['locale' => 'en'], [
            'name' => 'Vedas & Puranas',
            'description' => 'Sacred Vedic texts, 18 Mahapuranas, and Upanishads in digital edition.',
            'seo_title' => 'Download 4 Vedas & 18 Puranas in Hindi and English PDF',
            'seo_description' => 'Explore authentic digital editions of Rigveda, Samaveda, Yajurveda, Atharvaveda, and all 18 Mahapuranas.',
        ]);

        $vedasCat = Category::updateOrCreate(
            ['slug' => 'vedas'],
            ['sort_order' => 1, 'parent_id' => $vedPuranCat->id, 'is_active' => true]
        );
        $vedasCat->translations()->updateOrCreate(['locale' => 'hi'], [
            'name' => 'चार वेद',
            'description' => 'ऋग्वेद, यजुर्वेद, सामवेद, और अथर्ववेद संहिता।',
            'seo_title' => 'चारों वेद पीडीएफ डाउनलोड हिन्दी में — The 4 Vedas',
            'seo_description' => 'सम्पूर्ण 4 वेद संस्कृत एवं सरल हिन्दी अनुवाद में पीडीएफ पढ़ें।',
        ]);
        $vedasCat->translations()->updateOrCreate(['locale' => 'en'], [
            'name' => 'The 4 Vedas',
            'description' => 'Rigveda, Yajurveda, Samaveda, and Atharvaveda Samhitas.',
            'seo_title' => 'The 4 Vedas in Hindi & English PDF Download',
            'seo_description' => 'Read and download the 4 sacred Vedas in Hindi and English.',
        ]);

        $puranasCat = Category::updateOrCreate(
            ['slug' => 'puranas'],
            ['sort_order' => 2, 'parent_id' => $vedPuranCat->id, 'is_active' => true]
        );
        $puranasCat->translations()->updateOrCreate(['locale' => 'hi'], [
            'name' => '18 महापुराण',
            'description' => 'महर्षि वेदव्यास रचित अठारह महापुराण।',
            'seo_title' => '18 महापुराण पीडीएफ डाउनलोड हिन्दी में — All 18 Puranas PDF',
            'seo_description' => 'शिव पुराण, विष्णु पुराण, श्रीमद्भागवत, गरुड़ पुराण सहित सभी १८ महापुराण।',
        ]);
        $puranasCat->translations()->updateOrCreate(['locale' => 'en'], [
            'name' => '18 Mahapuranas',
            'description' => 'The 18 Mahapuranas authored by Sage Vedavyasa.',
            'seo_title' => 'All 18 Puranas in Hindi & English PDF Download',
            'seo_description' => 'Complete directory of 18 Mahapuranas in Hindi and English PDF.',
        ]);

        $chalisaCat = Category::updateOrCreate(
            ['slug' => 'chalisa-stotra'],
            ['sort_order' => 2, 'is_active' => true]
        );
        $chalisaCat->translations()->updateOrCreate(['locale' => 'hi'], [
            'name' => 'चालीसा व स्तोत्र',
            'description' => 'सम्पूर्ण चालीसा, स्तोत्र, आरती, भजन एवं स्तुति पाठ।',
            'seo_title' => 'चालीसा संग्रह एवं स्तोत्र पाठ — Chalisa Lyrics & PDF',
            'seo_description' => 'हनुमान चालीसा, शिव चालीसा, दुर्गा चालीसा सहित सभी देवी-देवताओं की चालीसा पाठ एवं पीडीएफ।',
        ]);
        $chalisaCat->translations()->updateOrCreate(['locale' => 'en'], [
            'name' => 'Chalisa & Stotra',
            'description' => 'Devotional chalisas, stotras, aartis, and hymns with lyrics & audio.',
            'seo_title' => 'Chalisa Collection with Hindi Lyrics & PDF Download',
            'seo_description' => 'Hanuman Chalisa, Shiv Chalisa, and all devotional hymns with complete text and audio.',
        ]);

        // 2. Authors
        $tulsidas = Author::updateOrCreate(['slug' => 'goswami-tulsidas'], ['is_active' => true]);
        $tulsidas->translations()->updateOrCreate(['locale' => 'hi'], [
            'name' => 'गोस्वामी तुलसीदास',
            'biography' => 'भक्तिकाल के शिरोमणि कवि, श्रीरामचरितमानस, हनुमान चालीसा, विनय पत्रिका और दोहावली के अमर रचयिता।',
        ]);
        $tulsidas->translations()->updateOrCreate(['locale' => 'en'], [
            'name' => 'Goswami Tulsidas',
            'biography' => 'Celebrated Bhakti poet and saint, author of Ramcharitmanas and Hanuman Chalisa.',
        ]);

        $vedavyasa = Author::updateOrCreate(['slug' => 'maharshi-vedavyasa'], ['is_active' => true]);
        $vedavyasa->translations()->updateOrCreate(['locale' => 'hi'], [
            'name' => 'महर्षि वेदव्यास',
            'biography' => 'वेदों के संकलनकर्ता, महाभारत एवं अठारह महापुराणों के दिव्य रचयिता महर्षि कृष्ण द्वैपायन वेदव्यास।',
        ]);
        $vedavyasa->translations()->updateOrCreate(['locale' => 'en'], [
            'name' => 'Maharshi Vedavyasa',
            'biography' => 'Compiler of the 4 Vedas and author of Mahabharata and the 18 Mahapuranas.',
        ]);

        $traditional = Author::updateOrCreate(['slug' => 'traditional-rishis'], ['is_active' => true]);
        $traditional->translations()->updateOrCreate(['locale' => 'hi'], [
            'name' => 'वैदिक ऋषि परम्परा',
            'biography' => 'सनातन धर्म के प्राचीन मंत्रद्रष्टा ऋषिगण जिन्होंने वेदों की ऋचाओं का साक्षात्कार किया।',
        ]);
        $traditional->translations()->updateOrCreate(['locale' => 'en'], [
            'name' => 'Vedic Rishi Tradition',
            'biography' => 'The ancient seers and rishis of the sacred Vedic oral tradition.',
        ]);

        // 3. Books & Texts
        // Book 1: Hanuman Chalisa
        $hanumanBook = Book::updateOrCreate(['slug' => 'hanuman-chalisa'], [
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
            'license_type' => 'Public Domain Heritage',
            'original_language_id' => $hindi?->id,
            'source_url' => 'https://archive.org/details/hanumanchalisa-hindi',
            'pages' => 8,
            'view_count' => 15800,
            'rating_average' => 5.0,
            'rating_count' => 128,
        ]);
        $hanumanBook->categories()->syncWithoutDetaching([$chalisaCat->id]);
        $hanumanBook->authors()->syncWithoutDetaching([$tulsidas->id]);
        if ($hindi) {
            $hanumanBook->languages()->syncWithoutDetaching([$hindi->id]);
        }

        $hanumanBook->translations()->updateOrCreate(['locale' => 'hi'], [
            'title' => 'श्री हनुमान चालीसा (Shree Hanuman Chalisa)',
            'short_description' => 'सम्पूर्ण हनुमान चालीसा: दोहा, ४० चौपाइयां, शुद्ध देवनागरी पाठ, अर्थ सहित ऑडियो एवं पीडीएफ।',
            'description' => 'श्री हनुमान चालीसा गोस्वामी तुलसीदास जी द्वारा अवधी भाषा में रचित एक अत्यंत प्रभावशाली एवं पावन भक्ति स्तुति है। इसमें पवनपुत्र श्री हनुमान जी के अतुलित बल, बुद्धि, विद्या और रामभक्ति का अनुपम गुणगान किया गया है।

यहाँ सम्पूर्ण हनुमान चालीसा का पाठ शुद्ध देवनागरी लिपि में प्रस्तुत है, जिसे आप फॉन्ट छोटा या बड़ा करके सुगमता से पढ़ सकते हैं, कॉपी कर सकते हैं और ऑडियो भी सुन सकते हैं।',
            'summary' => 'हनुमान चालीसा के चालीस पद्यों में बजरंगबली के पराक्रम, लंका दहन, संजीवनी बूटी आगमन, विभीषण मैत्री और भक्तों के सभी संकट हरने की दिव्य महिमा वर्णित है।',
            'why_read' => 'नित्य हनुमान चालीसा का पाठ करने से भय, व्याधि, संकट और नकारात्मक ऊर्जा का निवारण होता है तथा आत्मबल एवं शांति की प्राप्ति होती है।',
            'lyrics' => <<<'L'
॥ दोहा ॥
श्रीगुरु चरन सरोज रज, निज मनु मुकुरु सुधारि।
बरनऊँ रघुबर बिमल जसु, जो दायकु फल चारि॥
बुद्धिहीन तनु जानिके, सुमिरौ पवन-कुमार।
बल बुद्धि बिद्या देहु मोहिं, हरहु कलेस बिकार॥

॥ चौपाई ॥
जय हनुमान ज्ञान गुन सागर।
जय कपीस तिहुँ लोक उजागर॥
रामदूत अतुलित बल धामा।
अंजनि-पुत्र पवनसुत नामा॥

महाबीर बिक्रम बजरंगी।
कुमति निवार सुमति के संगी॥
कंचन बरन बिराज सुबेसा।
कानन कुंडल कुंचित केसा॥

हाथ बज्र औ ध्वजा बिराजै।
काँधे मूँज जनेऊ साजै॥
संकर सुवन केसरीनंदन।
तेज प्रताप महा जग बन्दन॥

बिद्यावान गुनी अति चातुर।
राम काज करिबे को आतुर॥
प्रभु चरित्र सुनिबे को रसिया।
राम लखन सीता मन बसिया॥

सूक्ष्म रूप धरि सियहिं दिखावा।
बिकट रूप धरि लंक जरावा॥
भीम रूप धरि असुर संहारे।
रामचंद्र के काज संवारे॥

लाय सजीवन लखन जियाये।
श्रीरघुबीर हरषि उर लाये॥
रघुपति कीन्ही बहुत बड़ाई।
तुम मम प्रिय भरतहि सम भाई॥

सहस बदन तुम्हरो जस गावैं।
अस कहि श्रीपति कंठ लगावैं॥
सनकादिक ब्रह्मादि मुनीसा।
नारद सारद सहित अहीसा॥

जम कुबेर दिगपाल जहाँ ते।
कबि कोबिद कहि सके कहाँ ते॥
तुम उपकार सुग्रीवहिं कीन्हा।
राम मिलाय राज पद दीन्हा॥

तुम्हरो मंत्र बिभीषन माना।
लंकेस्वर भए सब जग जाना॥
जुग सहस्र जोजन पर भानू।
लील्यो ताहि मधुर फल जानू॥

प्रभु मुद्रिका मेलि मुख माहीं।
जलधि लाँघि गये अचरज नाहीं॥
दुर्गम काज जगत के जेते।
सुगम अनुग्रह तुम्हरे तेते॥

राम दुआरे तुम रखवारे।
होत न आज्ञा बिनु पैसारे॥
सब सुख लहै तुम्हारी सरना।
तुम रक्षक काहू को डर ना॥

आपन तेज सम्हारो आपै।
तीनों लोक हाँक तें काँपै॥
भूत पिसाच निकट नहिं आवै।
महाबीर जब नाम सुनावै॥

नासै रोग हरै सब पीरा।
जपत निरंतर हनुमत बीरा॥
संकट तें हनुमान छुड़ावै।
मन क्रम बचन ध्यान जो लावै॥

सब पर राम तपस्वी राजा।
तिन के काज सकल तुम साजा॥
और मनोरथ जो कोई लावै।
सोइ अमित जीवन फल पावै॥

चारों जुग परताप तुम्हारा।
है परसिद्ध जगत उजियारा॥
साधु-संत के तुम रखवारे।
असुर निकंदन राम दुलारे॥

अष्ट सिद्धि नौ निधि के दाता।
अस बर दीन जानकी माता॥
राम रसायन तुम्हरे पासा।
सदा रहो रघुपति के दासा॥

तुम्हरे भजन राम को पावै।
जनम-जनम के दुख बिसरावै॥
अंत काल रघुबर पुर जाई।
जहाँ जन्म हरि-भक्त कहाई॥

और देवता चित्त न धरई।
हनुमत सेइ सर्ब सुख करई॥
संकट कटै मिटै सब पीरा।
जो सुमिरै हनुमत बलबीरा॥

जै जै जै हनुमान गोसाईं।
कृपा करहु गुरुदेव की नाईं॥
जो सत बार पाठ कर कोई।
छूटहि बंदि महा सुख होई॥

जो यह पढ़ै हनुमान चालीसा।
होय सिद्धि साखी गौरीसा॥
तुलसीदास सदा हरि चेरा।
कीजै नाथ हृदय महँ डेरा॥

॥ दोहा ॥
पवनतनय संकट हरन, मंगल मूरति रूप।
राम लखन सीता सहित, हृदय बसहु सुर भूप॥
L,
            'seo_title' => 'हनुमान चालीसा PDF व लिरिक्स अर्थ सहित — Shree Hanuman Chalisa Hindi Lyrics & PDF',
            'seo_description' => 'सम्पूर्ण हनुमान चालीसा पाठ (दोहा व 40 चौपाई) शुद्ध हिन्दी लिरिक्स, ऑडियो वाचन एवं निःशुल्क पीडीएफ डाउनलोड। Hanuman Chalisa lyrics with meaning in Hindi PDF.',
            'key_themes' => ['हनुमान चालीसा', 'हनुमान चालीसा लिरिक्स', 'हनुमान चालीसा पीडीएफ', 'hanuman chalisa lyrics', 'hanuman chalisa pdf'],
        ]);

        $hanumanBook->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Shree Hanuman Chalisa (With Lyrics & PDF)',
            'short_description' => 'Complete Hanuman Chalisa: Dohas, 40 Chaupais, authentic Hindi lyrics, audio recitation, and free PDF download.',
            'description' => 'Shree Hanuman Chalisa is a celebrated 40-verse hymn composed by saint-poet Goswami Tulsidas in praise of Lord Hanuman. It extols his immense strength, wisdom, devotion, and selfless service to Lord Rama.',
            'summary' => 'The 40 verses recount the exploits of Hanuman from childhood to the victory in Lanka, conferring fearlessness and spiritual peace to regular reciters.',
            'why_read' => 'Chanting Hanuman Chalisa provides courage, relieves distress, dispels negativity, and instills spiritual determination.',
            'lyrics' => <<<'L'
॥ दोहा ॥
श्रीगुरु चरन सरोज रज, निज मनु मुकुरु सुधारि।
बरनऊँ रघुबर बिमल जसु, जो दायकु फल चारि॥
बुद्धिहीन तनु जानिके, सुमिरौ पवन-कुमार।
बल बुद्धि बिद्या देहु मोहिं, हरहु कलेस बिकार॥

॥ चौपाई ॥
जय हनुमान ज्ञान गुन सागर।
जय कपीस तिहुँ लोक उजागर॥
रामदूत अतुलित बल धामा।
अंजनि-पुत्र पवनसुत नामा॥

महाबीर बिक्रम बजरंगी।
कुमति निवार सुमति के संगी॥
कंचन बरन बिराज सुबेसा।
कानन कुंडल कुंचित केसा॥

हाथ बज्र औ ध्वजा बिराजै।
काँधे मूँज जनेऊ साजै॥
संकर सुवन केसरीनंदन।
तेज प्रताप महा जग बन्दन॥

बिद्यावान गुनी अति चातुर।
राम काज करिबे को आतुर॥
प्रभु चरित्र सुनिबे को रसिया।
राम लखन सीता मन बसिया॥

सूक्ष्म रूप धरि सियहिं दिखावा।
बिकट रूप धरि लंक जरावा॥
भीम रूप धरि असुर संहारे।
रामचंद्र के काज संवारे॥

लाय सजीवन लखन जियाये।
श्रीरघुबीर हरषि उर लाये॥
रघुपति कीन्ही बहुत बड़ाई।
तुम मम प्रिय भरतहि सम भाई॥

सहस बदन तुम्हरो जस गावैं।
अस कहि श्रीपति कंठ लगावैं॥
सनकादिक ब्रह्मादि मुनीसा।
नारद सारद सहित अहीसा॥

जम कुबेर दिगपाल जहाँ ते।
कबि कोबिद कहि सके कहाँ ते॥
तुम उपकार सुग्रीवहिं कीन्हा।
राम मिलाय राज पद दीन्हा॥

तुम्हरो मंत्र बिभीषन माना।
लंकेस्वर भए सब जग जाना॥
जुग सहस्र जोजन पर भानू।
लील्यो ताहि मधुर फल जानू॥

प्रभु मुद्रिका मेलि मुख माहीं।
जलधि लाँघि गये अचरज नाहीं॥
दुर्गम काज जगत के जेते।
सुगम अनुग्रह तुम्हरे तेते॥

राम दुआरे तुम रखवारे।
होत न आज्ञा बिनु पैसारे॥
सब सुख लहै तुम्हारी सरना।
तुम रक्षक काहू को डर ना॥

आपन तेज सम्हारो आपै।
तीनों लोक हाँक तें काँपै॥
भूत पिसाच निकट नहिं आवै।
महाबीर जब नाम सुनावै॥

नासै रोग हरै सब पीरा।
जपत निरंतर हनुमत बीरा॥
संकट तें हनुमान छुड़ावै।
मन क्रम बचन ध्यान जो लावै॥

सब पर राम तपस्वी राजा।
तिन के काज सकल तुम साजा॥
और मनोरथ जो कोई लावै।
सोइ अमित जीवन फल पावै॥

चारों जुग परताप तुम्हारा।
है परसिद्ध जगत उजियारा॥
साधु-संत के तुम रखवारे।
असुर निकंदन राम दुलारे॥

अष्ट सिद्धि नौ निधि के दाता।
अस बर दीन जानकी माता॥
राम रसायन तुम्हरे पासा।
सदा रहो रघुपति के दासा॥

तुम्हरे भजन राम को पावै।
जनम-जनम के दुख बिसरावै॥
अंत काल रघुबर पुर जाई।
जहाँ जन्म हरि-भक्त कहाई॥

और देवता चित्त न धरई।
हनुमत सेइ सर्ब सुख करई॥
संकट कटै मिटै सब पीरा।
जो सुमिरै हनुमत बलबीरा॥

जै जै जै हनुमान गोसाईं।
कृपा करहु गुरुदेव की नाईं॥
जो सत बार पाठ कर कोई।
छूटहि बंदि महा सुख होई॥

जो यह पढ़ै हनुमान चालीसा।
होय सिद्धि साखी गौरीसा॥
तुलसीदास सदा हरि चेरा।
कीजै नाथ हृदय महँ डेरा॥

॥ दोहा ॥
पवनतनय संकट हरन, मंगल मूरति रूप।
राम लखन सीता सहित, हृदय बसहु सुर भूप॥
L,
            'seo_title' => 'Hanuman Chalisa PDF Download & Hindi Lyrics — Free Audio & Text',
            'seo_description' => 'Read complete Hanuman Chalisa lyrics in Hindi with meaning, listen to audio recitation, and download high-quality PDF for free.',
            'key_themes' => ['hanuman chalisa pdf', 'hanuman chalisa lyrics', 'hanuman chalisa in english', 'shree hanuman chalisa'],
        ]);

        // Book 2: Shiv Chalisa
        $shivBook = Book::updateOrCreate(['slug' => 'shiv-chalisa'], [
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
            'license_type' => 'Public Domain Heritage',
            'original_language_id' => $hindi?->id,
            'source_url' => 'https://archive.org/details/shiv-chalisa-hindi',
            'pages' => 6,
            'view_count' => 9400,
            'rating_average' => 4.9,
            'rating_count' => 84,
        ]);
        $shivBook->categories()->syncWithoutDetaching([$chalisaCat->id]);
        $shivBook->authors()->syncWithoutDetaching([$traditional->id]);
        if ($hindi) {
            $shivBook->languages()->syncWithoutDetaching([$hindi->id]);
        }

        $shivBook->translations()->updateOrCreate(['locale' => 'hi'], [
            'title' => 'श्री शिव चालीसा (Shree Shiv Chalisa)',
            'short_description' => 'सम्पूर्ण शिव चालीसा: दोहा, ४० चौपाइयां, महादेव की स्तुति, पाठ व निःशुल्क पीडीएफ।',
            'description' => 'श्री शिव चालीसा भगवान भोलेनाथ शंकर जी की अत्यंत पावन और फलदायी स्तुति है। इसमें देवाधिदेव महादेव के कल्याणकारी स्वरूप, जटा में गंगा, मस्तक पर चंद्रमा, और भक्तों पर असीम कृपा का वर्णन है।',
            'summary' => 'चालीस चौपाइयों में भगवान शिव के पंचानन स्वरूप, तांडव नृत्य, विषपान और कैलाशी स्वरूप का श्रद्धापूर्वक स्तवन किया गया है।',
            'why_read' => 'सोमवार अथवा प्रदोष काल में शिव चालीसा का पाठ करने से समस्त भय, व्याधियां और पारिवारिक कष्ट दूर होकर सुख-समृद्धि मिलती है।',
            'lyrics' => <<<'L'
॥ दोहा ॥
जय गणेश गिरिजा सुवन, मंगल मूल सुजान।
कहत अयोध्यादास तुम, देहु अभय वरदान॥

॥ चौपाई ॥
जय गिरिजा पति दीन दयाला।
सदा करत सन्तन प्रतिपाला॥
भाल चन्द्रमा सोहत नीके।
कानन कुण्डल नागफनी के॥

अंग गौर शिर गंग बहाये।
मुण्डमाल तन क्षार लगाये॥
वस्त्र खाल बाघम्बर सोहे।
छवि को देखि नाग मुनि मोहे॥

मैना मातु की ह्वै दुलारी।
बाम अंग सोहत छवि न्यारी॥
कर त्रिशूल सोहत शुचि भाल।
करत सदा शत्रुन क्षयकारी॥

नन्दी भृङ्गी नृत्य करावैं।
उपमन्यु मुनि ध्यान लगावैं॥
देव दनुज सब शीश नवावैं।
ऋषि मुनि यश गुणगान सुनावैं॥

काशी में विराजे विश्वनाथा।
त्रिभुवन नायक जग के त्राता॥
कैलाशी अविनाशी देवा।
सब जन करते तुम्हारी सेवा॥

जय जय जय जगदीश्वर स्वामी।
घट घट वासी अन्तरयामी॥
दीन हीन जन शरण तुम्हारी।
काटो संकट हे त्रिपुरारी॥

जो यह पाठ करै मन लाई।
तापर शम्भु करहिं सहाई॥
ऋद्धि सिद्धि उसके घर आवै।
जन्म मरण भव फन्द छुड़ावै॥
L,
            'seo_title' => 'शिव चालीसा PDF व लिरिक्स — Shree Shiv Chalisa Hindi Lyrics & PDF Download',
            'seo_description' => 'सम्पूर्ण श्री शिव चालीसा पाठ (दोहा व चौपाई) शुद्ध देवनागरी लिरिक्स, ऑडियो और पीडीएफ डाउनलोड। Shiv Chalisa Hindi lyrics and PDF free download.',
            'key_themes' => ['शिव चालीसा', 'शिव चालीसा पीडीएफ', 'शिव चालीसा लिरिक्स', 'shiv chalisa pdf', 'shiv chalisa lyrics'],
        ]);

        $shivBook->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Shree Shiv Chalisa (With Lyrics & PDF)',
            'short_description' => 'Complete Shree Shiv Chalisa: 40 verses in praise of Lord Shiva with Hindi lyrics, audio, and PDF.',
            'description' => 'Shree Shiv Chalisa is a sacred forty-verse prayer dedicated to Lord Shiva, the supreme ascetic and source of universal auspiciousness.',
            'summary' => 'The hymn celebrates Shiva as the destroyer of evil, protector of the downtrodden, and bestower of liberation.',
            'why_read' => 'Reciting Shiv Chalisa brings mental composure, removes planetary afflictions, and grants divine protection.',
            'lyrics' => <<<'L'
॥ दोहा ॥
जय गणेश गिरिजा सुवन, मंगल मूल सुजान।
कहत अयोध्यादास तुम, देहु अभय वरदान॥

॥ चौपाई ॥
जय गिरिजा पति दीन दयाला।
सदा करत सन्तन प्रतिपाला॥
भाल चन्द्रमा सोहत नीके।
कानन कुण्डल नागफनी के॥

अंग गौर शिर गंग बहाये।
मुण्डमाल तन क्षार लगाये॥
वस्त्र खाल बाघम्बर सोहे।
छवि को देखि नाग मुनि मोहे॥

मैना मातु की ह्वै दुलारी।
बाम अंग सोहत छवि न्यारी॥
कर त्रिशूल सोहत शुचि भाल।
करत सदा शत्रुन क्षयकारी॥

नन्दी भृङ्गी नृत्य करावैं।
उपमन्यु मुनि ध्यान लगावैं॥
देव दनुज सब शीश नवावैं।
ऋषि मुनि यश गुणगान सुनावैं॥

काशी में विराजे विश्वनाथा।
त्रिभुवन नायक जग के त्राता॥
कैलाशी अविनाशी देवा।
सब जन करते तुम्हारी सेवा॥

जय जय जय जगदीश्वर स्वामी।
घट घट वासी अन्तरयामी॥
दीन हीन जन शरण तुम्हारी।
काटो संकट हे त्रिपुरारी॥

जो यह पाठ करै मन लाई।
तापर शम्भु करहिं सहाई॥
ऋद्धि सिद्धि उसके घर आवै।
जन्म मरण भव फन्द छुड़ावै॥
L,
            'seo_title' => 'Shiv Chalisa PDF Download & Hindi Lyrics — Free Audio & Text',
            'seo_description' => 'Read complete Shiv Chalisa lyrics in Hindi, listen to audio recitation, and download free PDF edition.',
            'key_themes' => ['shiv chalisa pdf', 'shiv chalisa lyrics', 'shiva chalisa download'],
        ]);

        // Book 3: Rigveda Samhita
        $rigvedaBook = Book::updateOrCreate(['slug' => 'rigveda-samhita'], [
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
            'license_type' => 'Public Domain Heritage',
            'original_language_id' => $hindi?->id,
            'source_url' => 'https://archive.org/details/RigVedaWithHindiTranslationByPtRamGovindTrivedi1954',
            'pages' => 1240,
            'publication_year' => 1954,
            'publisher' => 'चौखम्बा / वैदिक अनुसंधान संस्थान',
            'view_count' => 14200,
            'rating_average' => 5.0,
            'rating_count' => 96,
        ]);
        $rigvedaBook->categories()->syncWithoutDetaching([$vedasCat->id, $vedPuranCat->id]);
        $rigvedaBook->authors()->syncWithoutDetaching([$traditional->id]);
        if ($hindi) {
            $rigvedaBook->languages()->syncWithoutDetaching([$hindi->id]);
        }

        $rigvedaBook->translations()->updateOrCreate(['locale' => 'hi'], [
            'title' => 'ऋग्वेद संहिता (Rigveda Samhita with Hindi Translation)',
            'short_description' => 'सम्पूर्ण ऋग्वेद संहिता: १० मण्डल, १०२८ सूक्त, मूल संस्कृत मन्त्र एवं सरल हिन्दी भाष्यानुवाद।',
            'description' => "ऋग्वेद सनातन धर्म का सर्वाधिक प्राचीन और पूज्य धर्मग्रंथ है। यह चारों वेदों में प्रथम एवं सर्वोच्च है। इसमें १० मण्डल और १०२८ सूक्त संगृहीत हैं। प्रथम मण्डल का प्रारंभ प्रसिद्ध 'अग्नि सूक्त' से होता है।

यहाँ ऋग्वेद का प्रामाणिक संस्करण, ऑनलाइन पाठ व पीडीएफ अध्ययन हेतु उपलब्ध है।",
            'summary' => 'ऋग्वेद में सृष्टि उत्पत्ति (नासदीय सूक्त), विराट पुरुष (पुरुष सूक्त), ज्ञान, प्रकृति और परमात्मा की स्तुति का अलौकिक भंडार है।',
            'why_read' => 'भारतीय संस्कृति, दर्शन और सनातन ज्ञान के मूल स्रोत को प्रामाणिक रूप से समझने के लिए ऋग्वेद का अध्ययन अनिवार्य है।',
            'lyrics' => <<<'L'
॥ ॐ श्री गणेशाय नमः ॥
॥ अथ ऋग्वेद संहिता — प्रथम मण्डल (अग्नि सूक्त) ॥

ॐ अग्निमीळे पुरोहितं यज्ञस्य देवमृत्विजम्।
होतारं रत्नधातमम्॥ १॥

अग्निः पूर्वेभिरृषिभिरीड्यो नूतनैरुत।
स देवाँ एह वक्षति॥ २॥

अग्निना रयिमश्नवत्पोषमेव दिवेदिवे।
यशसं वीरवत्तमम्॥ ३॥

अग्ने यं यज्ञमध्वरं विश्वतः परिभूरसि।
स इद्देवेषु गच्छति॥ ४॥

अग्निर्होता कविक्रतुः सत्यश्चित्रश्रवस्तमः।
देवो देवेभिरा गमत्॥ ५॥

यदङ्ग दाशुषे त्वमग्ने भद्रं करिष्यसि।
तवेत्तत्सत्यमंगिरः॥ ६॥

उप त्वाग्ने दिवेदिवे दोषावस्तर्धिया वयम्।
नमो भरन्त एमसि॥ ७॥

राजन्तमध्वराणां गोपाामृतस्य दीदिविम्।
वर्धमानं स्वे दमे॥ ८॥

स नः पितेव सूनवेऽग्ने सूपायनो भव।
सचस्वा नः स्वस्तये॥ ९॥
L,
            'seo_title' => 'ऋग्वेद संहिता PDF हिन्दी अनुवाद सहित — Rigveda in Hindi PDF Download',
            'seo_description' => 'सम्पूर्ण ऋग्वेद संहिता (10 मण्डल, 1028 सूक्त) मूल संस्कृत मन्त्र एवं सरल हिन्दी अर्थ सहित ऑनलाइन पढ़ें और पीडीएफ डाउनलोड करें।',
            'key_themes' => ['ऋग्वेद', 'ऋग्वेद पीडीएफ', 'चार वेद', 'rigveda in hindi pdf', 'all 4 vedas pdf'],
        ]);

        $rigvedaBook->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Rigveda Samhita (Complete with Translation & PDF)',
            'short_description' => 'Complete Rigveda Samhita: 10 Mandalas, 1028 Suktas with Sanskrit mantras and Hindi/English translation.',
            'description' => 'Rigveda is the oldest known Vedic Sanskrit text and the foundational scripture of Hinduism. Comprising 10 Mandalas and 1,028 hymns, it explores metaphysics, cosmology, and the divine forces of nature.',
            'summary' => 'Contains profound philosophical suktas including the Nasadiya Sukta (Hymn of Creation) and Purusha Sukta.',
            'why_read' => 'Essential for scholars and spiritual seekers exploring ancient civilization and the dawn of philosophical inquiry.',
            'lyrics' => <<<'L'
॥ ॐ श्री गणेशाय नमः ॥
॥ अथ ऋग्वेद संहिता — प्रथम मण्डल (अग्नि सूक्त) ॥

ॐ अग्निमीळे पुरोहितं यज्ञस्य देवमृत्विजम्।
होतारं रत्नधातमम्॥ १॥

अग्निः पूर्वेभिरृषिभिरीड्यो नूतनैरुत।
स देवाँ एह वक्षति॥ २॥

अग्निना रयिमश्नवत्पोषमेव दिवेदिवे।
यशसं वीरवत्तमम्॥ ३॥

अग्ने यं यज्ञमध्वरं विश्वतः परिभूरसि।
स इद्देवेषु गच्छति॥ ४॥

अग्निर्होता कविक्रतुः सत्यश्चित्रश्रवस्तमः।
देवो देवेभिरा गमत्॥ ५॥

यदङ्ग दाशुषे त्वमग्ने भद्रं करिष्यसि।
तवेत्तत्सत्यमंगिरः॥ ६॥

उप त्वाग्ने दिवेदिवे दोषावस्तर्धिया वयम्।
नमो भरन्त एमसि॥ ७॥

राजन्तमध्वराणां गोपाामृतस्य दीदिविम्।
वर्धमानं स्वे दमे॥ ८॥

स नः पितेव सूनवेऽग्ने सूपायनो भव।
सचस्वा नः स्वस्तये॥ ९॥
L,
            'seo_title' => 'Rigveda PDF Download in Hindi & English — The First Veda',
            'seo_description' => 'Download complete Rigveda Samhita PDF in Hindi and English. Read online with Sanskrit mantras and commentary.',
            'key_themes' => ['rigveda pdf', 'rigveda in english pdf', 'download 4 vedas pdf'],
        ]);

        // Book 4: Shrimad Bhagavata Mahapurana
        $bhagwatBook = Book::updateOrCreate(['slug' => 'bhagwat-puran'], [
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
            'license_type' => 'Public Domain Heritage',
            'original_language_id' => $hindi?->id,
            'source_url' => 'https://archive.org/details/shrimad-bhagavata-purana-hindi-gita-press',
            'pages' => 1480,
            'publication_year' => 2012,
            'publisher' => 'गीताप्रेस गोरखपुर',
            'view_count' => 18600,
            'rating_average' => 5.0,
            'rating_count' => 152,
        ]);
        $bhagwatBook->categories()->syncWithoutDetaching([$puranasCat->id, $vedPuranCat->id]);
        $bhagwatBook->authors()->syncWithoutDetaching([$vedavyasa->id]);
        if ($hindi) {
            $bhagwatBook->languages()->syncWithoutDetaching([$hindi->id]);
        }

        $bhagwatBook->translations()->updateOrCreate(['locale' => 'hi'], [
            'title' => 'श्रीमद्भागवत महापुराण (Shrimad Bhagavata Purana)',
            'short_description' => 'सम्पूर्ण १२ स्कन्ध, १८,००० श्लोक, सरल हिन्दी अनुवाद सहित गीताप्रेस गोरखपुर संस्करण।',
            'description' => 'श्रीमद्भागवत महापुराण अठारह पुराणों का मुकुटमणि ग्रंथ है। इसमें भगवान श्रीकृष्ण के अवतारी चरित्र, भक्ति योग, ज्ञान और वैराग्य का अद्भुत विवेचन है। यह कलयुग में साक्षात् श्रीकृष्ण स्वरूप माना जाता है।',
            'summary' => '१२ स्कन्धों में परीक्षित-शुकदेव संवाद, ध्रुव-प्रह्लाद चरित्र, रासपंचाध्यायी और एकादश स्कन्ध का उद्धव गीता उपदेश संगृहीत है।',
            'why_read' => 'भागवत श्रवण और पठन से मनुष्य के संशय मिटते हैं, चित्त में परम शांति आती है और मोक्ष मार्ग प्रशस्त होता है।',
            'lyrics' => '॥ श्रीहरिः ॥
॥ श्रीमद्भागवत महापुराण — मङ्गलाचरणम् ॥

जन्माद्यस्य यतोऽन्वयादितरतश्चार्थेष्वभिज्ञः स्वराट्
तेने ब्रह्म हृदा य आदिकवये मुह्यन्ति यत्सूरयः।
तेजोवारिमृदां यथा विनिमयो यत्र त्रिसर्गोऽमृषा
धाम्ना स्वेन सदा निरस्तकुहकं सत्यं परं धीमहि॥

धर्मः प्रोज्झितकैतवोऽत्र परमो निर्मत्सराणां सतां
वेद्यं वास्तवमत्र वस्तु शिवदं तापत्रयोन्मूलनम्।
श्रीमद्भागवते महामुनिकृते किं वा परैरीश्वरः
सद्यो हृद्यवरुध्यतेऽत्र कृतिभिः शुश्रूषुभिस्तत्क्षणात्॥

॥ ॐ नमो भगवते वासुदेवाय ॥',
            'seo_title' => 'श्रीमद्भागवत महापुराण PDF हिन्दी में — Bhagwat Puran PDF Download',
            'seo_description' => 'सम्पूर्ण श्रीमद्भागवत महापुराण 12 स्कन्ध (गीताप्रेस गोरखपुर) सरल हिन्दी अनुवाद सहित ऑनलाइन पढ़ें और पीडीएफ डाउनलोड करें।',
            'key_themes' => ['भागवत पुराण', 'श्रीमद्भागवत महापुराण', 'bhagwat puran pdf', '18 puran pdf in hindi'],
        ]);

        $bhagwatBook->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Shrimad Bhagavata Purana (Complete 12 Skandhas PDF)',
            'short_description' => 'The crown jewel of all 18 Puranas in 12 Cantos with Sanskrit verses and Hindi/English translation.',
            'description' => 'Shrimad Bhagavata Mahapurana is revered as the ultimate scripture of Bhakti and non-dual realization, focusing on the cosmic pastimes and teachings of Lord Krishna.',
            'summary' => 'Chronicles the dialogue between King Parikshit and Sage Shukadeva across 12 Cantos and 18,000 verses.',
            'why_read' => 'Regarded as the ripe fruit of Vedic knowledge (Nigama-kalpataror galitam phalam), granting supreme spiritual illumination.',
            'lyrics' => 'Om Namo Bhagavate Vasudevaya

Satyam Param Dhimahi — We meditate upon the supreme absolute Truth from whom all creation, sustenance, and dissolution emerge.',
            'seo_title' => 'Bhagavata Purana PDF Download — Complete 12 Cantos in Hindi & English',
            'seo_description' => 'Download Shrimad Bhagavata Purana complete PDF in Hindi and English. Read online with Gitapress Gorakhpur edition.',
            'key_themes' => ['bhagavata purana pdf', 'bhagwat puran download', 'all 18 puranas pdf'],
        ]);

        // Book 5: Shiva Purana
        $shivaBook = Book::updateOrCreate(['slug' => 'shiva-puran'], [
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
            'license_type' => 'Public Domain Heritage',
            'original_language_id' => $hindi?->id,
            'source_url' => 'https://archive.org/details/shiva-purana-hindi-gita-press',
            'pages' => 1120,
            'publication_year' => 2014,
            'publisher' => 'गीताप्रेस गोरखपुर',
            'view_count' => 16400,
            'rating_average' => 4.9,
            'rating_count' => 134,
        ]);
        $shivaBook->categories()->syncWithoutDetaching([$puranasCat->id, $vedPuranCat->id]);
        $shivaBook->authors()->syncWithoutDetaching([$vedavyasa->id]);
        if ($hindi) {
            $shivaBook->languages()->syncWithoutDetaching([$hindi->id]);
        }

        $shivaBook->translations()->updateOrCreate(['locale' => 'hi'], [
            'title' => 'शिव महापुराण (Shiva Mahapurana in Hindi)',
            'short_description' => 'सम्पूर्ण शिव महापुराण: विद्येश्वर, रुद्र, शतरुद्र, कोटिरुद्र एवं उमा संहिता सरल हिन्दी में।',
            'description' => 'शिव महापुराण में देवाधिदेव भगवान शिव की महिमा, शिवलिंग पूजन का रहस्य, द्वादश ज्योतिर्लिंगों का उद्भव, रुद्राक्ष एवं भस्म माहात्म्य का प्रामाणिक निरूपण है।',
            'summary' => '७ संहिताओं और २४,००० श्लोकों में सती चरित्र, पार्वती विवाह, कार्तिकेय-गणेश जन्म और तारकासुर वध का दिव्य आख्यान है।',
            'why_read' => 'शिव पुराण का पठन अथवा श्रवण करने से अकाल मृत्यु का भय समाप्त होता है और मनोवांछित फल की प्राप्ति होती है।',
            'lyrics' => '॥ ॐ नमः शिवाय ॥
॥ शिव महापुराण — मङ्गलाचरणम् ॥

वन्दे शैलसुतापतिं सुरगुरुं वन्दे जगत्कारणं
वन्दे पन्नगभूषणं मृगधरं वन्दे पशूनां पतिम्।
वन्दे सूर्यशशांकवह्निनयनं वन्दे मुकुन्दप्रियं
वन्दे भक्तजनाश्रयं च वरदं वन्दे शिवं शंकरम्॥

॥ ॐ तत्पुरुषाय विद्महे महादेवाय धीमहि तन्नो रुद्रः प्रचोदयात् ॥',
            'seo_title' => 'शिव महापुराण PDF डाउनलोड हिन्दी में — Shiva Puran PDF Gita Press',
            'seo_description' => 'सम्पूर्ण शिव महापुराण (गीताप्रेस गोरखपुर) सरल हिन्दी अनुवाद सहित पढ़ें और निःशुल्क पीडीएफ डाउनलोड करें। Shiva Purana in Hindi PDF.',
            'key_themes' => ['शिव पुराण', 'शिव महापुराण', 'shiva puran pdf', '18 puran pdf in hindi'],
        ]);

        $shivaBook->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Shiva Mahapurana (Complete Hindi Edition PDF)',
            'short_description' => 'Complete Shiva Purana containing Vidyeshwara, Rudra, and Kotirudra Samhitas with Hindi commentary.',
            'description' => 'The Shiva Purana is one of the eighteen major Puranas, detailing the cosmology, mythology, pilgrimage sites, and yoga of Lord Shiva.',
            'summary' => 'Across 7 Samhitas, it describes the origin of the 12 Jyotirlingas, the marriage of Shiva and Parvati, and the worship of the Lingam.',
            'why_read' => 'Provides supreme metaphysical insights into Shaiva theology and meditation.',
            'lyrics' => 'Om Namah Shivaya

Vande Shailasutapatim Suragurum Vande Jagatkaranam — Salutations to Lord Shiva, the beloved consort of Parvati and source of the universe.',
            'seo_title' => 'Shiva Purana PDF Download in Hindi & English — Free Full Book',
            'seo_description' => 'Download complete Shiva Purana PDF in Hindi and English. Gitapress Gorakhpur authentic digital edition.',
            'key_themes' => ['shiva puran pdf', 'shiva purana in english', 'all 18 puranas pdf'],
        ]);

        // Book 6: Garuda Purana
        $garudaBook = Book::updateOrCreate(['slug' => 'garuda-puran'], [
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
            'license_type' => 'Public Domain Heritage',
            'original_language_id' => $hindi?->id,
            'source_url' => 'https://archive.org/details/garuda-purana-hindi-gita-press',
            'pages' => 380,
            'publication_year' => 2016,
            'publisher' => 'गीताप्रेस गोरखपुर',
            'view_count' => 17200,
            'rating_average' => 4.8,
            'rating_count' => 110,
        ]);
        $garudaBook->categories()->syncWithoutDetaching([$puranasCat->id, $vedPuranCat->id]);
        $garudaBook->authors()->syncWithoutDetaching([$vedavyasa->id]);
        if ($hindi) {
            $garudaBook->languages()->syncWithoutDetaching([$hindi->id]);
        }

        $garudaBook->translations()->updateOrCreate(['locale' => 'hi'], [
            'title' => 'गरुड़ पुराण (Garuda Purana Saroddhara in Hindi)',
            'short_description' => 'सम्पूर्ण गरुड़ पुराण: प्रेतकल्प, धर्म-कर्म, मरणोपरांत जीवात्मा की गति एवं मोक्ष मार्ग।',
            'description' => 'गरुड़ पुराण में भगवान विष्णु और उनके वाहन पक्षीराज गरुड़ के बीच मृत्यु, यमलोक, कर्मों के फल, श्राद्ध विधि और मुक्ति का गहन संवाद वर्णित है।',
            'summary' => '१९,००० श्लोकों में मानव शरीर की क्षणभंगुरता, सदाचार, दान-पुण्य और परलोक की यात्रा का सजीव निरूपण है।',
            'why_read' => 'कर्म सिद्धांत, धर्म की महत्ता और आत्मा की अमरता को समझकर श्रेष्ठ जीवन जीने के लिए गरुड़ पुराण अत्यंत प्रेरणादायी है।',
            'lyrics' => '॥ श्रीहरिः ॥
॥ गरुड़ पुराण सारोद्धार — मङ्गलाचरणम् ॥

यं शैवाः समुपासते शिव इति ब्रह्मेति वेदान्तिनो
बौद्धा बुद्ध इति प्रमाणपटवः कर्तेति नैयायिकाः।
अर्हन्नित्यथ जैनशासनरताः कर्मेति मीमांसकाः
सोऽयं वो विदधातु वाञ्छितफलं त्रैलोक्यनाथो हरिः॥

॥ ॐ नमो भगवते वासुदेवाय नमः ॥',
            'seo_title' => 'गरुड़ पुराण PDF हिन्दी में — Garuda Puran in Hindi PDF Download',
            'seo_description' => 'सम्पूर्ण गरुड़ पुराण (प्रेतकल्प व सारोद्धार) गीताप्रेस गोरखपुर सरल हिन्दी अनुवाद सहित ऑनलाइन पढ़ें और पीडीएफ डाउनलोड करें।',
            'key_themes' => ['गरुड़ पुराण', 'garuda puran pdf', 'garuda puran in hindi', '18 puran pdf in hindi'],
        ]);

        $garudaBook->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Garuda Purana (Complete Hindi & English Edition PDF)',
            'short_description' => 'Garuda Purana: The dialogue between Lord Vishnu and Garuda on karma, afterlife, and liberation.',
            'description' => 'The Garuda Purana is one of the Vaishnava Puranas dealing with death, the journey of the soul, funeral rites, and spiritual ethics.',
            'summary' => 'Explains the law of karma, consequences of actions in the afterlife, and methods of attaining eternal liberation.',
            'why_read' => 'Offers profound reflections on righteous living, charity, and understanding the impermanence of mortal life.',
            'lyrics' => 'Om Namo Bhagavate Vasudevaya

May Lord Hari, who is worshipped under various names by devotees, grant the desired fruits of virtuous life.',
            'seo_title' => 'Garuda Purana PDF Download in Hindi & English — Free Full Book',
            'seo_description' => 'Download complete Garuda Purana PDF in Hindi and English. Gitapress Gorakhpur authentic digital edition.',
            'key_themes' => ['garuda puran pdf', 'garuda purana download', 'all 18 puranas pdf'],
        ]);

        // Book 7: Vishnu Purana
        $vishnuBook = Book::updateOrCreate(['slug' => 'vishnu-puran'], [
            'status' => BookStatus::Published->value,
            'copyright_status' => CopyrightStatus::PublicDomain->value,
            'license_type' => 'Public Domain Heritage',
            'original_language_id' => $hindi?->id,
            'source_url' => 'https://archive.org/details/vishnu-purana-hindi-gita-press',
            'pages' => 540,
            'publication_year' => 2015,
            'publisher' => 'गीताप्रेस गोरखपुर',
            'view_count' => 11200,
            'rating_average' => 4.9,
            'rating_count' => 88,
        ]);
        $vishnuBook->categories()->syncWithoutDetaching([$puranasCat->id, $vedPuranCat->id]);
        $vishnuBook->authors()->syncWithoutDetaching([$vedavyasa->id]);
        if ($hindi) {
            $vishnuBook->languages()->syncWithoutDetaching([$hindi->id]);
        }

        $vishnuBook->translations()->updateOrCreate(['locale' => 'hi'], [
            'title' => 'विष्णु पुराण (Vishnu Purana in Hindi)',
            'short_description' => 'सम्पूर्ण ६ अंश, २३,००० श्लोक, पराशर-मैत्रेय संवाद, सृष्टि वर्णन एवं अवतार चरित्र।',
            'description' => 'विष्णु पुराण प्राचीनतम एवं सबसे व्यवस्थित महापुराणों में से एक है। इसमें महर्षि पराशर ने अपने शिष्य मैत्रेय को सृष्टि की उत्पत्ति, काल गणना, ध्रुव-प्रह्लाद चरित्र और भगवान विष्णु के दशावतारों का उपदेश दिया है।',
            'summary' => '६ अंशों में भूगोल, खगोल, सूर्यवंश, चंद्रवंश, कृष्ण चरित्र और कलयुग वर्णन का विशद निरूपण है।',
            'why_read' => 'पुराण-लक्षणों (सर्ग, प्रतिसर्ग, वंश, मन्वन्तर, वंशानुचरित) से युक्त सर्वाधिक शास्त्रीय पुराण होने के कारण यह अत्यंत पठनीय है।',
            'lyrics' => '॥ श्रीहरिः ॥
॥ श्रीविष्णुपुराणम् — मङ्गलाचरणम् ॥

ॐ नमो भगवते वासुदेवाय॥

यः संस्थितौ जगतोऽस्य बीजं
यस्मिन् स्थितौ यत्र च लीयतेऽसौ।
तमीश्वरं सत्यमनन्तमक्षरं
परं पदं विष्णुमजं प्रपद्ये॥

शान्ताकारं भुजगशयनं पद्मनाभं सुरेशं
विश्वाधारं गगनसदृशं मेघवर्णं शुभाङ्गम्।
लक्ष्मीकान्तं कमलनयनं योगिभिर्ध्यानगम्यं
वन्दे विष्णुं भवभयहरं सर्वलोकैकनाथम्॥',
            'seo_title' => 'विष्णु पुराण PDF हिन्दी में — Vishnu Puran in Hindi PDF Download',
            'seo_description' => 'सम्पूर्ण श्रीविष्णु पुराण (गीताप्रेस गोरखपुर) सरल हिन्दी अनुवाद सहित ऑनलाइन पढ़ें और निःशुल्क पीडीएफ डाउनलोड करें।',
            'key_themes' => ['विष्णु पुराण', 'vishnu puran pdf', '18 puran pdf in hindi', 'vishnu purana download'],
        ]);

        $vishnuBook->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'Vishnu Purana (Complete Hindi & English Edition PDF)',
            'short_description' => 'One of the most authentic and ancient Mahapuranas across 6 Sections with Hindi/English translation.',
            'description' => 'The Vishnu Purana is considered the model text of all Puranas, presenting the 5 canonical Purana-lakshanas through the discourse of Sage Parashara to Maitreya.',
            'summary' => 'Covers the cosmology of the universe, cosmic cycles (manvantaras), the story of Prahlada, and the life of Lord Krishna.',
            'why_read' => 'Celebrated for its poetic beauty, systematic theology, and philosophical clarity.',
            'lyrics' => 'Om Namo Bhagavate Vasudevaya

Shantakaram Bhujagashayanam Padmanabham Suresham — Salutations to Lord Vishnu, the embodiment of peace and sole protector of all the worlds.',
            'seo_title' => 'Vishnu Purana PDF Download in Hindi & English — Free Full Book',
            'seo_description' => 'Download complete Vishnu Purana PDF in Hindi and English. Gitapress Gorakhpur authentic digital edition.',
            'key_themes' => ['vishnu purana pdf', 'vishnu puran download', 'all 18 puranas pdf'],
        ]);
    }
}
