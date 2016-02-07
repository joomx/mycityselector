
CREATE TABLE IF NOT EXISTS `#__mycityselector_city` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` TEXT NOT NULL,
  `status` tinyint NOT NULL,
  `ordering` INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

ALTER TABLE `#__mycityselector_city` ADD PRIMARY KEY (`id`);

ALTER TABLE `#__mycityselector_city` MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

INSERT INTO `#__mycityselector_city` (`name`, `content`, `ordering`) VALUES
('First city', 'This can be content of item', '1'),
('Second city', 'This can be content of item', '2');