--
-- Структура таблицы `modx_csvcatalog_config`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}csvcatalog_config` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `setting` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `config` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `modx_csvcatalog_config_name`
--

CREATE TABLE IF NOT EXISTS `{PREFIX}csvcatalog_config_name` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;