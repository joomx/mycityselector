
CREATE TABLE IF NOT EXISTS `#__mycityselector_country` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__mycityselector_region` (
  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `country_id` int(11),
  `name` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `subdomain` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__mycityselector_city` (
  `id` bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `country_id` int(11),
  `region_id` int(11),
  `name` varchar(50) NOT NULL,
  `subdomain` varchar(50) NOT NULL,
  `status` tinyint NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

#страны
INSERT INTO `#__mycityselector_country` (`id`, `name`) VALUES (1, 'Россия');

# регионы
INSERT INTO `#__mycityselector_region` (`id`, `country_id`, `name`, `district`, `subdomain`) VALUES
(1, 1, 'Республика Адыгея', 'Южный', 'respublika-adygeya'),
(2, 1, 'Республика Башкортостан', 'Приволжский', 'respublika-bashkortostan'),
(3, 1, 'Республика Бурятия', 'Сибирский', 'respublika-buryatiya'),
(4, 1, 'Республика Алтай', 'Сибирский', 'respublika-altaj'),
(5, 1, 'Республика Дагестан', 'Северо-Кавказский', 'respublika-dagestan'),
(6, 1, 'Республика Ингушетия', 'Северо-Кавказский', 'respublika-ingushetiya'),
(7, 1, 'Кабардино-Балкарская Республика', 'Северо-Кавказский', 'kabardino-balkarskaya-respublika'),
(8, 1, 'Республика Калмыкия', 'Южный', 'respublika-kalmykiya'),
(9, 1, 'Республика Карачаево-Черкесия', 'Северо-Кавказский', 'respublika-karachaevo-cherkesiya'),
(10, 1, 'Республика Карелия', 'Северо-Западный', 'respublika-kareliya'),
(11, 1, 'Республика Коми', 'Северо-Западный', 'respublika-komi'),
(12, 1, 'Республика Марий Эл', 'Приволжский', 'respublika-marij-el'),
(13, 1, 'Республика Мордовия', 'Приволжский', 'respublika-mordoviya'),
(14, 1, 'Республика Саха (Якутия)', 'Дальневосточный', 'respublika-saha-yakutiya'),
(15, 1, 'Республика Северная Осетия-Алания', 'Северо-Кавказский', 'respublika-severnaya-osetiya-alaniya'),
(16, 1, 'Республика Татарстан', 'Приволжский', 'respublika-tatarstan'),
(17, 1, 'Республика Тыва', 'Сибирский', 'respublika-tyva'),
(18, 1, 'Удмуртская Республика', 'Приволжский', 'udmurtskaya-respublika'),
(19, 1, 'Республика Хакасия', 'Сибирский', 'respublika-hakasiya'),
(20, 1, 'Чеченская республика', '(новый номер - 95)', 'chechenskaya-respublika'),
(21, 1, 'Чувашская Республика', 'Приволжский', 'chuvashskaya-respublika'),
(22, 1, 'Алтайский край', 'Сибирский', 'altajskij-kraj'),
(23, 1, 'Краснодарский край', 'Южный', 'krasnodarskij-kraj'),
(24, 1, 'Красноярский край', 'Сибирский', 'krasnoyarskij-kraj'),
(25, 1, 'Приморский край', 'Дальневосточный', 'primorskij-kraj'),
(26, 1, 'Ставропольский край', 'Северо-Кавказский', 'stavropolskij-kraj'),
(27, 1, 'Хабаровский край', 'Дальневосточный', 'habarovskij-kraj'),
(28, 1, 'Амурская область', 'Дальневосточный', 'amurskaya-oblast'),
(29, 1, 'Архангельская область', 'Северо-Западный', 'arhangelskaya-oblast'),
(30, 1, 'Астраханская область', 'Южный', 'astrahanskaya-oblast'),
(31, 1, 'Белгородская область', 'Центральный', 'belgorodskaya-oblast'),
(32, 1, 'Брянская область', 'Центральный', 'bryanskaya-oblast'),
(33, 1, 'Владимирская область', 'Центральный', 'vladimirskaya-oblast'),
(34, 1, 'Волгоградская область', 'Южный', 'volgogradskaya-oblast'),
(35, 1, 'Вологодская область', 'Северо-Западный', 'vologodskaya-oblast'),
(36, 1, 'Воронежская область', 'Центральный', 'voronezhskaya-oblast'),
(37, 1, 'Ивановская область', 'Центральный', 'ivanovskaya-oblast'),
(38, 1, 'Иркутская область', 'Сибирский', 'irkutskaya-oblast'),
(39, 1, 'Калининградская область', 'Северо-Западный', 'kaliningradskaya-oblast'),
(40, 1, 'Калужская область', 'Центральный', 'kaluzhskaya-oblast'),
(41, 1, 'Камчатский край', 'Дальневосточный', 'kamchatskij-kraj'),
(42, 1, 'Камчатская область', '(вошла в Камчатский край)', 'kamchatskaya-oblast'),
(43, 1, 'Кемеровская область', 'Сибирский', 'kemerovskaya-oblast'),
(44, 1, 'Кировская область', 'Приволжский', 'kirovskaya-oblast'),
(45, 1, 'Костромская область', 'Центральный', 'kostromskaya-oblast'),
(46, 1, 'Курганская область', 'Уральский', 'kurganskaya-oblast'),
(47, 1, 'Курская область', 'Центральный', 'kurskaya-oblast'),
(48, 1, 'Ленинградская область', 'Северо-Западный', 'leningradskaya-oblast'),
(49, 1, 'Липецкая область', 'Центральный', 'lipetskaya-oblast'),
(50, 1, 'Магаданская область', 'Дальневосточный', 'magadanskaya-oblast'),
(51, 1, 'Московская область', 'Центральный', 'moskovskaya-oblast'),
(52, 1, 'Мурманская область', 'Северо-Западный', 'murmanskaya-oblast'),
(53, 1, 'Нижегородская область', 'Приволжский', 'nizhegorodskaya-oblast'),
(54, 1, 'Новгородская область', 'Северо-Западный', 'novgorodskaya-oblast'),
(55, 1, 'Новосибирская область', 'Сибирский', 'novosibirskaya-oblast'),
(56, 1, 'Омская область', 'Сибирский', 'omskaya-oblast'),
(57, 1, 'Оренбургская область', 'Приволжский', 'orenburgskaya-oblast'),
(58, 1, 'Орловская область', 'Центральный', 'orlovskaya-oblast'),
(59, 1, 'Пензенская область', 'Приволжский', 'penzenskaya-oblast'),
(60, 1, 'Пермский край', 'Приволжский', 'permskij-kraj'),
(61, 1, 'Пермская область', '(вошла в Пермский край)', 'permskaya-oblast'),
(62, 1, 'Псковская область', 'Северо-Западный', 'pskovskaya-oblast'),
(63, 1, 'Ростовская область', 'Южный', 'rostovskaya-oblast'),
(64, 1, 'Рязанская область', 'Центральный', 'ryazanskaya-oblast'),
(65, 1, 'Самарская область', 'Приволжский', 'samarskaya-oblast'),
(66, 1, 'Саратовская область', 'Приволжский', 'saratovskaya-oblast'),
(67, 1, 'Сахалинская область', 'Дальневосточный', 'sahalinskaya-oblast'),
(68, 1, 'Свердловская область', 'Уральский', 'sverdlovskaya-oblast'),
(69, 1, 'Смоленская область', 'Центральный', 'smolenskaya-oblast'),
(70, 1, 'Тамбовская область', 'Центральный', 'tambovskaya-oblast'),
(71, 1, 'Тверская область', 'Центральный', 'tverskaya-oblast'),
(72, 1, 'Томская область', 'Сибирский', 'tomskaya-oblast'),
(73, 1, 'Тульская область', 'Центральный', 'tulskaya-oblast'),
(74, 1, 'Тюменская область', 'Уральский', 'tyumenskaya-oblast'),
(75, 1, 'Ульяновская область', 'Приволжский', 'ulyanovskaya-oblast'),
(76, 1, 'Челябинская область', 'Уральский', 'chelyabinskaya-oblast'),
(77, 1, 'Забайкальский край', 'Сибирский', 'zabajkalskij-kraj'),
(78, 1, 'Читинская область', '(вошла в Забайкальский край)', 'chitinskaya-oblast'),
(79, 1, 'Ярославская область', 'Центральный', 'yaroslavskaya-oblast'),
(80, 1, 'г. Москва', 'Центральный', 'g.-moskva'),
(81, 1, 'г. Санкт-Петербург', 'Северо-Западный', 'g.-sankt-peterburg'),
(82, 1, 'Еврейская автономная область', 'Дальневосточный', 'evrejskaya-avtonomnaya-oblast'),
(83, 1, 'Агинский Бурятский автономный округ', '(вошел в Забайкальский край)', 'aginskij-buryatskij-avtonomnyj-okrug'),
(84, 1, 'Коми-Пермяцкий автономный округ', '(вошел в Пермский край)', 'komi-permyatskij-avtonomnyj-okrug'),
(85, 1, 'Корякский автономный округ', '(вошел в Камчатский край)', 'koryakskij-avtonomnyj-okrug'),
(86, 1, 'Ненецкий автономный округ', 'Северо-Западный', 'nenetskij-avtonomnyj-okrug'),
(87, 1, 'Таймырский (Долгано-Ненецкий) автономный округ', '(вошел в Красноярский край)', 'tajmyrskij-dolgano-nenetskij-avtonomnyj-'),
(88, 1, 'Усть-Ордынский Бурятский автономный округ', '(вошел в Иркутскую обл.)', 'ust-ordynskij-buryatskij-avtonomnyj-okru'),
(89, 1, 'Ханты-Мансийский автономный округ - Югра', 'Уральский', 'hanty-mansijskij-avtonomnyj-okrug---yugr'),
(90, 1, 'Чукотский автономный округ', 'Дальневосточный', 'chukotskij-avtonomnyj-okrug'),
(91, 1, 'Эвенкийский автономный округ', '(вошел в Красноярский край)', 'evenkijskij-avtonomnyj-okrug'),
(92, 1, 'Ямало-Ненецкий автономный округ', 'Уральский', 'yamalo-nenetskij-avtonomnyj-okrug'),
(93, 1, 'Республика Крым', 'Крымский', 'respublika-krym'),
(94, 1, 'г.Севастополь', 'Крымский', 'g.sevastopol'),
(95, 1, 'Чеченская Республика', 'Северо-Кавказский', 'chechenskaya-respublika');


