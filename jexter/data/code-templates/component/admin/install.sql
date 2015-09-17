CREATE TABLE IF NOT EXISTS `#__sitemapjen_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `loc` varchar(255) NOT NULL,
  `lastmod` date NOT NULL,
  `changefreq` varchar(8) NOT NULL,
  `priority` varchar(3) NOT NULL,
  `md5_content` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__sitemapjen_options` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `param` varchar(20) NOT NULL,
  `title` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `#__sitemapjen_options` (`param`,`title`,`value`) VALUES
('task_url','Адрес начала сканирования',''),
('task_action','Текущий процесс',''),
('task_status','Статус задачи',''),
('task_step','Позиция в задаче','0'),
('task_gentype','Тип генерации sitemap','1'),
('task_lastreqtime','Время последнего запроса',''),
('last_starttime','Последний запуск','неизвестно'),
('ignore_list','Список исключаемых url',''),
('ignore_option_com','Исключать адреса типа option=com_','N'),
('ignore_nofollow','Исключать адреса rel=nofollow','Y'),
('threads','Количество потоков','3'),
('only_4pu','Исключать адреса вида "?query=value&uqery2=value2..."','N');