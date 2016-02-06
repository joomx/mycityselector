
CREATE TABLE IF NOT EXISTS `#__{jex_table_name}` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` TEXT NOT NULL,
  `status` tinyint NOT NULL,
  `ordering` INT NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8  AUTO_INCREMENT=1;

ALTER TABLE `#__{jex_table_name}` ADD PRIMARY KEY (`id`);

ALTER TABLE `#__{jex_table_name}` MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

INSERT INTO `#__{jex_table_name}` (`name`, `content`, `ordering`) VALUES
('First {jex_item_model}', 'This can be content of item', '1'),
('Second {jex_item_model}', 'This can be content of item', '2');