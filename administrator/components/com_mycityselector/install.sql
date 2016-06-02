CREATE TABLE IF NOT EXISTS `#__mycityselector_country` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `subdomain` varchar(50) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__mycityselector_country` (`id`, `name`, `subdomain`, `status`) VALUES (1, 'Россия', 'russia', 1);
INSERT INTO `#__mycityselector_country` (`id`, `name`, `subdomain`, `status`) VALUES (2, 'Беларусь', 'belarus', 1);
INSERT INTO `#__mycityselector_country` (`id`, `name`, `subdomain`, `status`) VALUES (3, 'Україна', 'ukraine', 1);

CREATE TABLE IF NOT EXISTS `#__mycityselector_region` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `country_id` int(11),
  `name` varchar(50) NOT NULL,
  `subdomain` varchar(50) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__mycityselector_region` (`country_id`, `name`, `subdomain`) VALUES
(1, 'Алтайский край', 'altajskij-kraj'),
(1, 'Амурская область', 'amurskaya-oblast'),
(1, 'Архангельская область', 'arhangelskaya-oblast'),
(1, 'Астраханская область', 'astrahanskaya-oblast'),
(1, 'Белгородская область', 'belgorodskaya-oblast'),
(1, 'Брянская область', 'bryanskaya-oblast'),
(1, 'Владимирская область', 'vladimirskaya-oblast'),
(1, 'Волгоградская область', 'volgogradskaya-oblast'),
(1, 'Вологодская область', 'vologodskaya-oblast'),
(1, 'Воронежская область', 'voronezhskaya-oblast'),
(1, 'Еврейская автономная область', 'evrejskaya-avtonomnaya-oblast'),
(1, 'Забайкальский край', 'zabajkalskij-kraj'),
(1, 'Ивановская область', 'ivanovskaya-oblast'),
(1, 'Иркутская область', 'irkutskaya-oblast'),
(1, 'Кабардино-Балкарская Республика', 'kabardino-balkarskaya-respublika'),
(1, 'Калининградская область', 'kaliningradskaya-oblast'),
(1, 'Калужская область', 'kaluzhskaya-oblast'),
(1, 'Камчатский край', 'kamchatskij-kraj'),
(1, 'Карачаево-Черкесская Республика', 'karachaevo-cherkesskaya-respublika'),
(1, 'Кемеровская область', 'kemerovskaya-oblast'),
(1, 'Кировская область', 'kirovskaya-oblast'),
(1, 'Костромская область', 'kostromskaya-oblast'),
(1, 'Краснодарский край', 'krasnodarskij-kraj'),
(1, 'Красноярский край', 'krasnoyarskij-kraj'),
(1, 'Курганская область', 'kurganskaya-oblast'),
(1, 'Курская область', 'kurskaya-oblast'),
(1, 'Ленинградская область', 'leningradskaya-oblast'),
(1, 'Липецкая область', 'lipetskaya-oblast'),
(1, 'Магаданская область', 'magadanskaya-oblast'),
(1, 'Москва', 'moskva'),
(1, 'Московская область', 'moskovskaya-oblast'),
(1, 'Мурманская область', 'murmanskaya-oblast'),
(1, 'Ненецкий автономный округ', 'nenetskij-avtonomnyj-okrug'),
(1, 'Нижегородская область', 'nizhegorodskaya-oblast'),
(1, 'Новгородская область', 'novgorodskaya-oblast'),
(1, 'Новосибирская область', 'novosibirskaya-oblast'),
(1, 'Омская область', 'omskaya-oblast'),
(1, 'Оренбургская область', 'orenburgskaya-oblast'),
(1, 'Орловская область', 'orlovskaya-oblast'),
(1, 'Пензенская область', 'penzenskaya-oblast'),
(1, 'Пермский край', 'permskij-kraj'),
(1, 'Приморский край', 'primorskij-kraj'),
(1, 'Псковская область', 'pskovskaya-oblast'),
(1, 'Республика Адыгея', 'respublika-adygeya'),
(1, 'Республика Алтай', 'respublika-altaj'),
(1, 'Республика Башкортостан', 'respublika-bashkortostan'),
(1, 'Республика Бурятия', 'respublika-buryatiya'),
(1, 'Республика Дагестан', 'respublika-dagestan'),
(1, 'Республика Ингушетия', 'respublika-ingushetiya'),
(1, 'Республика Калмыкия', 'respublika-kalmykiya'),
(1, 'Республика Карелия', 'respublika-kareliya'),
(1, 'Республика Коми', 'respublika-komi'),
(1, 'Республика Крым', 'respublika-krym'),
(1, 'Республика Марий Эл', 'respublika-marij-el'),
(1, 'Республика Мордовия', 'respublika-mordoviya'),
(1, 'Республика Саха', 'respublika-saha'),
(1, 'Республика Северная Осетия', 'respublika-severnaya-osetiya'),
(1, 'Республика Татарстан', 'respublika-tatarstan'),
(1, 'Республика Тыва', 'respublika-tyva'),
(1, 'Республика Хакасия', 'respublika-hakasiya'),
(1, 'Ростовская область', 'rostovskaya-oblast'),
(1, 'Рязанская область', 'ryazanskaya-oblast'),
(1, 'Самарская область', 'samarskaya-oblast'),
(1, 'Санкт-Петербург', 'sankt-peterburg'),
(1, 'Саратовская область', 'saratovskaya-oblast'),
(1, 'Сахалинская область', 'sahalinskaya-oblast'),
(1, 'Свердловская область', 'sverdlovskaya-oblast'),
(1, 'Севастополь', 'sevastopol'),
(1, 'Смоленская область', 'smolenskaya-oblast'),
(1, 'Ставропольский край', 'stavropolskij-kraj'),
(1, 'Тамбовская область', 'tambovskaya-oblast'),
(1, 'Тверская область', 'tverskaya-oblast'),
(1, 'Томская область', 'tomskaya-oblast'),
(1, 'Тульская область', 'tulskaya-oblast'),
(1, 'Тюменская область', 'tyumenskaya-oblast'),
(1, 'Удмуртская Республика', 'udmurtskaya-respublika'),
(1, 'Ульяновская область', 'ulyanovskaya-oblast'),
(1, 'Хабаровский край', 'habarovskij-kraj'),
(1, 'Ханты-Мансийский автономный округ', 'hanty-mansijskij-avtonomnyj-okrug'),
(1, 'Челябинская область', 'chelyabinskaya-oblast'),
(1, 'Чеченская Республика', 'chechenskaya-respublika'),
(1, 'Чувашская Республика', 'chuvashskaya-respublika'),
(1, 'Чукотский автономный округ', 'chukotskij-avtonomnyj-okrug'),
(1, 'Ямало-Ненецкий автономный округ', 'yamalo-nenetskij-avtonomnyj-okrug'),
(1, 'Ярославская область', 'yaroslavskaya-oblast');

CREATE TABLE IF NOT EXISTS `#__mycityselector_city` (
  `id` bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `country_id` int(11),
  `region_id` int(11),
  `name` varchar(50) NOT NULL,
  `subdomain` varchar(50) NOT NULL,
  `status` tinyint NOT NULL,
  `ordering` int(11)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,1,'Барнаул','barnaul',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,1,'Бийск','bijsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,1,'Рубцовск','rubtsovsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,2,'Благовещенск','blagoveschensk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,3,'Архангельск','arhangelsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,3,'Северодвинск','severodvinsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,4,'Астрахань','astrahan',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,5,'Белгород','belgorod',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,5,'Старый Оскол','staryj-oskol',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,6,'Брянск','bryansk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,7,'Владимир','vladimir',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,7,'Ковров','kovrov',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,7,'Муром','murom',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,8,'Волгоград','volgograd',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,8,'Волжский','volzhskij',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,8,'Камышин','kamyshin',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,9,'Череповец','cherepovets',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,9,'Вологда','vologda',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,10,'Воронеж','voronezh',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,12,'Чита','chita',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,13,'Иваново','ivanovo',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,14,'Иркутск','irkutsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,14,'Братск','bratsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,14,'Ангарск','angarsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,15,'Нальчик','nalchik',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,16,'Калининград','kaliningrad',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,17,'Калуга','kaluga',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,17,'Обнинск','obninsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,18,'Петропавловск-Камчатский','petropavlovsk-kamchatskij',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,19,'Черкесск','cherkessk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,20,'Новокузнецк','novokuznetsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,20,'Кемерово','kemerovo',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,20,'Прокопьевск','prokopevsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,21,'Киров','kirov',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,22,'Кострома','kostroma',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,23,'Краснодар','krasnodar',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,23,'Сочи','sochi',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,23,'Новороссийск','novorossijsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,23,'Армавир','armavir',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,24,'Красноярск','krasnoyarsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,24,'Норильск','norilsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,24,'Ачинск','achinsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,25,'Курган','kurgan',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,26,'Курск','kursk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,27,'Санкт-Петербург','sankt-peterburg',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,28,'Липецк','lipetsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,28,'Елец','elets',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,29,'Магадан','magadan',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,30,'Москва','moskva',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Балашиха','balashiha',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Химки','himki',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Подольск','podolsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Королёв','korolev',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Люберцы','lyubertsy',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Мытищи','mytischi',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Электросталь','elektrostal',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Коломна','kolomna',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Одинцово','odintsovo',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Красногорск','krasnogorsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Серпухов','serpuhov',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Орехово-Зуево','orehovo-zuevo',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Щёлково','schelkovo',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Домодедово','domodedovo',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Жуковский','zhukovskij',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Сергиев Посад','sergiev-posad',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Пушкино','pushkino',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Раменское','ramenskoe',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Ногинск','noginsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,31,'Долгопрудный','dolgoprudnyj',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,32,'Мурманск','murmansk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,34,'Нижний Новгород','nizhnij-novgorod',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,34,'Дзержинск','dzerzhinsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,34,'Арзамас','arzamas',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,35,'Великий Новгород','velikij-novgorod',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,36,'Новосибирск','novosibirsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,36,'Бердск','berdsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,37,'Омск','omsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,38,'Оренбург','orenburg',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,38,'Орск','orsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,39,'Орёл','orel',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,40,'Пенза','penza',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,41,'Пермь','perm',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,41,'Березники','berezniki',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,42,'Владивосток','vladivostok',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,42,'Уссурийск','ussurijsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,42,'Находка','nahodka',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,42,'Артём','artem',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,43,'Псков','pskov',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,44,'Майкоп','majkop',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,45,'Барнаул','barnaul',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,45,'Бийск','bijsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,45,'Рубцовск','rubtsovsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,46,'Уфа','ufa',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,46,'Стерлитамак','sterlitamak',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,46,'Салават','salavat',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,46,'Нефтекамск','neftekamsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,46,'Октябрьский','oktyabrskij',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,47,'Улан-Удэ','ulan-ude',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,48,'Махачкала','mahachkala',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,48,'Хасавюрт','hasavyurt',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,48,'Дербент','derbent',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,48,'Каспийск','kaspijsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,49,'Назрань','nazran',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,50,'Элиста','elista',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,51,'Петрозаводск','petrozavodsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,52,'Сыктывкар','syktyvkar',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,53,'Симферополь','simferopol',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,53,'Керчь','kerch',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,53,'Евпатория','evpatoriya',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,54,'Йошкар-Ола','joshkar-ola',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,55,'Саранск','saransk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,57,'Владикавказ','vladikavkaz',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,58,'Казань','kazan',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,58,'Набережные Челны','naberezhnye-chelny',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,58,'Нижнекамск','nizhnekamsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,58,'Альметьевск','almetevsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,59,'Кызыл','kyzyl',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,60,'Абакан','abakan',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,61,'Ростов-на-Дону','rostov-na-donu',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,61,'Таганрог','taganrog',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,61,'Шахты','shahty',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,61,'Новочеркасск','novocherkassk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,61,'Волгодонск','volgodonsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,61,'Батайск','batajsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,61,'Новошахтинск','novoshahtinsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,62,'Рязань','ryazan',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,63,'Самара','samara',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,63,'Тольятти','tolyatti',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,63,'Сызрань','syzran',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,63,'Новокуйбышевск','novokujbyshevsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,64,'Санкт-Петербург','sankt-peterburg',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,65,'Саратов','saratov',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,65,'Энгельс','engels',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,65,'Балаково','balakovo',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,66,'Южно-Сахалинск','yuzhno-sahalinsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,67,'Екатеринбург','ekaterinburg',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,67,'Нижний Тагил','nizhnij-tagil',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,67,'Каменск-Уральский','kamensk-uralskij',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,67,'Первоуральск','pervouralsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,68,'Севастополь','sevastopol',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,69,'Смоленск','smolensk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,70,'Ставрополь','stavropol',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,70,'Пятигорск','pyatigorsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,70,'Кисловодск','kislovodsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,70,'Невинномысск','nevinnomyssk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,70,'Ессентуки','essentuki',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,71,'Тамбов','tambov',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,72,'Тверь','tver',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,73,'Томск','tomsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,73,'Северск','seversk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,74,'Тула','tula',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,74,'Новомосковск','novomoskovsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,75,'Тюмень','tyumen',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,76,'Ижевск','izhevsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,77,'Ульяновск','ulyanovsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,77,'Димитровград','dimitrovgrad',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,78,'Хабаровск','habarovsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,78,'Комсомольск-на-Амуре','komsomolsk-na-amure',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,79,'Сургут','surgut',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,79,'Нижневартовск','nizhnevartovsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,79,'Нефтеюганск','nefteyugansk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,80,'Челябинск','chelyabinsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,80,'Магнитогорск','magnitogorsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,80,'Златоуст','zlatoust',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,80,'Миасс','miass',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,80,'Копейск','kopejsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,81,'Грозный','groznyj',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,82,'Чебоксары','cheboksary',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,82,'Новочебоксарск','novocheboksarsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,84,'Новый Уренгой','novyj-urengoj',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,84,'Ноябрьск','noyabrsk',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,85,'Ярославль','yaroslavl',0,NULL);
INSERT INTO `#__mycityselector_city` (`country_id`,`region_id`,`name`,`subdomain`,`status`,`ordering`) VALUES (1,85,'Рыбинск','rybinsk',0,NULL);
