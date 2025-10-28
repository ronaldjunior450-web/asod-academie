// Données des villes par pays
const villesParPays = {
    'Cameroun': [
        'Douala', 'Yaoundé', 'Garoua', 'Bamenda', 'Maroua', 'Nkongsamba', 'Bafoussam', 
        'Ngaoundéré', 'Bertoua', 'Loum', 'Kumba', 'Ebolowa', 'Kribi', 'Foumban',
        'Dschang', 'Limbe', 'Buea', 'Mbalmayo', 'Edea', 'Kousseri', 'Guider',
        'Meiganga', 'Yagoua', 'Mokolo', 'Bafia', 'Wum', 'Idenau', 'Tiko',
        'Kumbo', 'Nkambe', 'Fundong', 'Bali', 'Bamessing', 'Bamunka', 'Bamenda',
        'Bafut', 'Mankon', 'Bambili', 'Bambui', 'Bamenda', 'Bali', 'Bamessing'
    ],
    'Bénin': [
        'Cotonou', 'Porto-Novo', 'Parakou', 'Djougou', 'Bohicon', 'Abomey', 'Natitingou',
        'Lokossa', 'Ouidah', 'Savalou', 'Kandi', 'Malanville', 'Comé', 'Allada',
        'Aplahoué', 'Bassila', 'Bembèrèkè', 'Dassa-Zoumè', 'Glazoué', 'Kérou',
        'Kétou', 'Mono', 'Nikki', 'Ouèssè', 'Pobè', 'Sakété', 'Tchaourou',
        'Zagnanado', 'Zogbodomey', 'Adjohoun', 'Athiémé', 'Bantè', 'Bonou',
        'Dangbo', 'Grand-Popo', 'Houéyogbé', 'Ifangni', 'Kpomassè', 'Lalo',
        'Lokossa', 'Mono', 'Ouinhi', 'Pobè', 'Sè', 'Sèmè-Kpodji', 'Toffo',
        'Tori-Bossito', 'Zè'
    ],
    'Togo': [
        'Lomé', 'Sokodé', 'Kara', 'Kpalimé', 'Atakpamé', 'Bassar', 'Tsévié',
        'Aného', 'Sansanné-Mango', 'Dapaong', 'Tchamba', 'Niamtougou', 'Badou',
        'Vogan', 'Tabligbo', 'Kandé', 'Notsè', 'Amou', 'Haho', 'Zio',
        'Ogou', 'Tchamba', 'Kozah', 'Bassar', 'Tchaoudjo', 'Kéran', 'Doufelgou',
        'Tone', 'Cinkassé', 'Kpendjal', 'Oti', 'Tandjouaré', 'Assoli', 'Bimah',
        'Est-Mono', 'Kpélé', 'Lacs', 'Moyen-Mono', 'Notsè', 'Ogou', 'Sotouboua',
        'Tchamba', 'Tchaoudjo', 'Vo', 'Yoto', 'Zio'
    ],
    'Côte d\'Ivoire': [
        'Abidjan', 'Bouaké', 'Daloa', 'San-Pédro', 'Yamoussoukro', 'Korhogo', 'Man',
        'Gagnoa', 'Divo', 'Anyama', 'Bingerville', 'Bondoukou', 'Ferkessédougou',
        'Katiola', 'Lakota', 'Odienné', 'Séguéla', 'Soubré', 'Toumodi', 'Zuenoula',
        'Aboisso', 'Adzopé', 'Agboville', 'Agnibilékrou', 'Akoupé', 'Alépé',
        'Bocanda', 'Bonoua', 'Boundiali', 'Dabakala', 'Daoukro', 'Dimbokro',
        'Duékoué', 'Guiglo', 'Issia', 'Jacqueville', 'M\'Bahiakro', 'M\'Batto',
        'Oumé', 'Sakassou', 'Sassandra', 'Sinfra', 'Tiassalé', 'Touba',
        'Vavoua', 'Yopougon'
    ],
    'Sénégal': [
        'Dakar', 'Thiès', 'Kaolack', 'Ziguinchor', 'Saint-Louis', 'Diourbel',
        'Louga', 'Tambacounda', 'Kolda', 'Matam', 'Fatick', 'Kaffrine',
        'Sédhiou', 'Kédougou', 'Rufisque', 'Mbour', 'Touba', 'Tivaouane',
        'Joal-Fadiouth', 'Richard-Toll', 'Bignona', 'Kédougou', 'Kolda',
        'Linguère', 'Mbacké', 'Nioro du Rip', 'Podor', 'Sédhiou', 'Tambacounda',
        'Vélingara', 'Ziguinchor'
    ],
    'Mali': [
        'Bamako', 'Sikasso', 'Ségou', 'Mopti', 'Koutiala', 'Kayes', 'Kita',
        'San', 'Koulikoro', 'Djenné', 'Gao', 'Tombouctou', 'Kidal', 'Ansongo',
        'Bandiagara', 'Bourem', 'Douentza', 'Goundam', 'Kéniéba', 'Koro',
        'Macina', 'Markala', 'Ménaka', 'Nara', 'Niono', 'Nioro du Sahel',
        'Taoudéni', 'Ténenkou', 'Tessalit', 'Yorosso', 'Yélimané'
    ],
    'Burkina Faso': [
        'Ouagadougou', 'Bobo-Dioulasso', 'Koudougou', 'Banfora', 'Ouahigouya',
        'Kaya', 'Tenkodogo', 'Dédougou', 'Koupéla', 'Fada N\'Gourma', 'Dori',
        'Gaoua', 'Loropeni', 'Manga', 'Nouna', 'Pô', 'Réo', 'Sapouy',
        'Séguénéga', 'Sindou', 'Tougan', 'Yako', 'Ziniaré', 'Zorgo'
    ],
    'Niger': [
        'Niamey', 'Zinder', 'Maradi', 'Tahoua', 'Agadez', 'Dosso', 'Tillabéri',
        'Diffa', 'Arlit', 'Birni-N\'Konni', 'Gaya', 'Magaria', 'Matamey',
        'Téra', 'Tibiri', 'Tchirozérine', 'Ayorou', 'Filingué', 'Gouré',
        'Illéla', 'Kantché', 'Keita', 'Loga', 'Madarounfa', 'Mayahi',
        'Mirriah', 'N\'Guigmi', 'Ouallam', 'Say', 'Tchadoua', 'Tessaoua'
    ],
    'Tchad': [
        'N\'Djamena', 'Moundou', 'Sarh', 'Abéché', 'Kélo', 'Koumra', 'Pala',
        'Am Timan', 'Bongor', 'Doba', 'Faya-Largeau', 'Lai', 'Mongo',
        'Moussoro', 'Oum Hadjer', 'Zouar', 'Ati', 'Biltine', 'Bokoro',
        'Bousso', 'Fada', 'Goz Beïda', 'Iriba', 'Kyabé', 'Mangalmé',
        'Massakory', 'Massaguet', 'Melfi', 'Nokou', 'Oum Hadjer', 'Pala'
    ],
    'République Centrafricaine': [
        'Bangui', 'Bimbo', 'Berbérati', 'Carnot', 'Bambari', 'Bouar', 'Bossangoa',
        'Bria', 'Bangassou', 'Nola', 'Kaga-Bandoro', 'Sibut', 'Mbaïki',
        'Bozoum', 'Paoua', 'Bouca', 'Kabo', 'Batangafo', 'Alindao', 'Birao',
        'Mobaye', 'Zémio', 'Obo', 'Bakouma', 'Kouango', 'Gambo', 'Rafai',
        'Kembe', 'Yalinga', 'Bakala', 'Grimari', 'Kouango', 'Mingala'
    ],
    'Gabon': [
        'Libreville', 'Port-Gentil', 'Franceville', 'Oyem', 'Moanda', 'Lambaréné',
        'Mouila', 'Koulamoutou', 'Tchibanga', 'Makokou', 'Bitam', 'Ntoum',
        'Gamba', 'Omboué', 'Mayumba', 'Ndendé', 'Mimongo', 'Lastoursville',
        'Bongoville', 'Mékambo', 'Booué', 'Mounana', 'Okondja', 'Lékoni',
        'Mbigou', 'Mitzic', 'Cocobeach', 'Ndjolé', 'Fougamou', 'Lalara'
    ],
    'Congo': [
        'Brazzaville', 'Pointe-Noire', 'Dolisie', 'Nkayi', 'Owando', 'Ouesso',
        'Madingou', 'Kinkala', 'Mossendjo', 'Gamboma', 'Mossaka', 'Impfondo',
        'Makoua', 'Djambala', 'Ewo', 'Kellé', 'Mbomo', 'Sembé', 'Souanké',
        'Tchikapika', 'Bétou', 'Bouansa', 'Divénié', 'Komono', 'Lékana',
        'Mabombo', 'Mbandza-Ndounga', 'Mindouli', 'Ngabé', 'Ngoua', 'Nkomo'
    ],
    'République Démocratique du Congo': [
        'Kinshasa', 'Lubumbashi', 'Mbuji-Mayi', 'Kananga', 'Kisangani', 'Bukavu',
        'Goma', 'Kolwezi', 'Likasi', 'Matadi', 'Mbandaka', 'Uvira', 'Kikwit',
        'Tshikapa', 'Kalemie', 'Bunia', 'Butembo', 'Beni', 'Kindu', 'Maniema',
        'Kabinda', 'Kamina', 'Kasongo', 'Kabalo', 'Kongolo', 'Moba', 'Pweto',
        'Sakania', 'Kasumbalesa', 'Dilolo', 'Kipushi', 'Kipushi', 'Lukala'
    ],
    'Rwanda': [
        'Kigali', 'Butare', 'Gitarama', 'Ruhengeri', 'Gisenyi', 'Byumba',
        'Cyangugu', 'Kibuye', 'Rwamagana', 'Kibungo', 'Nyanza', 'Gikongoro',
        'Kibungo', 'Rusizi', 'Nyagatare', 'Gatsibo', 'Kayonza', 'Rwamagana',
        'Bugesera', 'Kirehe', 'Ngoma', 'Gisagara', 'Nyaruguru', 'Huye',
        'Nyanza', 'Ruhango', 'Muhanga', 'Kamonyi', 'Karongi', 'Rubavu'
    ],
    'Burundi': [
        'Bujumbura', 'Gitega', 'Ngozi', 'Rumonge', 'Cibitoke', 'Karuzi',
        'Kayanza', 'Kirundo', 'Makamba', 'Muramvya', 'Muyinga', 'Mwaro',
        'Rutana', 'Ruyigi', 'Bubanza', 'Bururi', 'Cankuzo', 'Isale',
        'Mabanda', 'Mugamba', 'Mugongomanga', 'Muhuta', 'Mukike', 'Mukungu',
        'Murago', 'Musongati', 'Mutambu', 'Mutumba', 'Muyinga', 'Nyanza-Lac'
    ],
    'Autre': [
        'Autre ville'
    ]
};

// Fonction pour obtenir les villes d'un pays
function getVillesByPays(pays) {
    return villesParPays[pays] || villesParPays['Autre'];
}

// Fonction pour mettre à jour la liste des villes
function updateVillesList(pays) {
    const villeSelect = document.getElementById('ville');
    const villes = getVillesByPays(pays);
    
    // Vider la liste actuelle
    villeSelect.innerHTML = '<option value="">Sélectionner une ville...</option>';
    
    // Ajouter les nouvelles villes
    villes.forEach(ville => {
        const option = document.createElement('option');
        option.value = ville;
        option.textContent = ville;
        villeSelect.appendChild(option);
    });
    
    console.log(`✅ Villes mises à jour pour ${pays}: ${villes.length} villes disponibles`);
}

