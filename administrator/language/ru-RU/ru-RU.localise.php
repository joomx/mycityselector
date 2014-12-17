<?php
/**
 * @package    Joomla.Language
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * ru-RU localise class
 *
 * @package  Joomla.Language
 * @since    1.6
 */
abstract class ru_RULocalise
{
	/**
	 * Returns the potential suffixes for a specific number of items
	 *
	 * @param	int $count  The number of items.
	 * @return	array  An array of potential suffixes.
	 * @since	1.6
	 */
	public static function getPluralSuffixes($count)
	{
		if ($count == 0) {
			$return = array('0');
		} else {
			$return = array(($count%10==1 && $count%100!=11 ? '1' : ($count%10>=2 && $count%10<=4 && ($count%100<10 || $count%100>=20) ? '2' : 'MORE')));
		}
		return $return;
	}

	public static function transliterate($string)
	{
		$str = JString::strtolower($string);

		$glyph_array = array(
			'a' => 'а',
			'b' => 'б',
			'v' => 'в',
			'g' => 'г,ґ',
			'd' => 'д',
			'e' => 'е,є,э',
			'jo' => 'ё',
			'zh' => 'ж',
			'z' => 'з',
			'i' => 'и,і',
			'ji' => 'ї',
			'j' => 'й',
			'k' => 'к',
			'l' => 'л',
			'm' => 'м',
			'n' => 'н',
			'o' => 'о',
			'p' => 'п',
			'r' => 'р',
			's' => 'с',
			't' => 'т',
			'u' => 'у',
			'f' => 'ф',
			'kh' => 'х',
			'ts' => 'ц',
			'ch' => 'ч',
			'sh' => 'ш',
			'shch' => 'щ',
			'' => 'ъ',
			'y' => 'ы',
			'' => 'ь',
			'yu' => 'ю',
			'ya' => 'я',
		);

		foreach ($glyph_array as $letter => $glyphs) {
			$glyphs = explode(',', $glyphs);
			$str = str_replace($glyphs, $letter, $str);
		}

		$str = preg_replace('#\&\#?[a-z0-9]+\;#ismu', '', $str);

		return $str;
	}

	/**
	 * Returns the ignored search words
	 *
	 * @return	array  An array of ignored search words.
	 * @since	1.6
	 */
	public static function getIgnoredSearchWords()
	{
		$search_ignore = array();
		$search_ignore[] = 'href';
		$search_ignore[] = 'lol';
		$search_ignore[] = 'www';
		$search_ignore[] = 'а';
		$search_ignore[] = 'без';
		$search_ignore[] = 'благодарю';
		$search_ignore[] = 'благодаря';
		$search_ignore[] = 'близко';
		$search_ignore[] = 'более';
		$search_ignore[] = 'более-менее';
		$search_ignore[] = 'большая';
		$search_ignore[] = 'больше';
		$search_ignore[] = 'большие';
		$search_ignore[] = 'большое';
		$search_ignore[] = 'большой';
		$search_ignore[] = 'будет';
		$search_ignore[] = 'будут';
		$search_ignore[] = 'будучи';
		$search_ignore[] = 'был';
		$search_ignore[] = 'была';
		$search_ignore[] = 'были';
		$search_ignore[] = 'было';
		$search_ignore[] = 'быть';
		$search_ignore[] = 'в';
		$search_ignore[] = 'вас';
		$search_ignore[] = 'вам';
		$search_ignore[] = 'вами';
		$search_ignore[] = 'ваш';
		$search_ignore[] = 'ваша';
		$search_ignore[] = 'ваше';
		$search_ignore[] = 'вашего';
		$search_ignore[] = 'вашей';
		$search_ignore[] = 'вашем';
		$search_ignore[] = 'вашему';
		$search_ignore[] = 'ваши';
		$search_ignore[] = 'вашим';
		$search_ignore[] = 'вашими';
		$search_ignore[] = 'ваших';
		$search_ignore[] = 'вашу';
		$search_ignore[] = 'вблизи';
		$search_ignore[] = 'ведь';
		$search_ignore[] = 'везде';
		$search_ignore[] = 'великолепно';
		$search_ignore[] = 'вне';
		$search_ignore[] = 'внутри';
		$search_ignore[] = 'во';
		$search_ignore[] = 'возле';
		$search_ignore[] = 'вот';
		$search_ignore[] = 'временами';
		$search_ignore[] = 'временем';
		$search_ignore[] = 'времени';
		$search_ignore[] = 'время';
		$search_ignore[] = 'все';
		$search_ignore[] = 'все-таки';
		$search_ignore[] = 'всё';
		$search_ignore[] = 'всё-таки';
		$search_ignore[] = 'всегда';
		$search_ignore[] = 'всего';
		$search_ignore[] = 'всеми';
		$search_ignore[] = 'всех';
		$search_ignore[] = 'всякая';
		$search_ignore[] = 'всякие';
		$search_ignore[] = 'всякий';
		$search_ignore[] = 'всяким';
		$search_ignore[] = 'всякими';
		$search_ignore[] = 'всяких';
		$search_ignore[] = 'всякого';
		$search_ignore[] = 'всякое';
		$search_ignore[] = 'всякой';
		$search_ignore[] = 'всяком';
		$search_ignore[] = 'всякому';
		$search_ignore[] = 'всякую';
		$search_ignore[] = 'выглядит';
		$search_ignore[] = 'выглядят';
		$search_ignore[] = 'где';
		$search_ignore[] = 'да';
		$search_ignore[] = 'далеко';
		$search_ignore[] = 'действительно';
		$search_ignore[] = 'день';
		$search_ignore[] = 'для';
		$search_ignore[] = 'дне';
		$search_ignore[] = 'дней';
		$search_ignore[] = 'днем';
		$search_ignore[] = 'днём';
		$search_ignore[] = 'дни';
		$search_ignore[] = 'дню';
		$search_ignore[] = 'дня';
		$search_ignore[] = 'дням';
		$search_ignore[] = 'днями';
		$search_ignore[] = 'днях';
		$search_ignore[] = 'до';
		$search_ignore[] = 'дом';
		$search_ignore[] = 'дома';
		$search_ignore[] = 'доме';
		$search_ignore[] = 'домом';
		$search_ignore[] = 'дому';
		$search_ignore[] = 'его';
		$search_ignore[] = 'ее';
		$search_ignore[] = 'её';
		$search_ignore[] = 'если';
		$search_ignore[] = 'есть';
		$search_ignore[] = 'еще';
		$search_ignore[] = 'же';
		$search_ignore[] = 'за';
		$search_ignore[] = 'зачем';
		$search_ignore[] = 'зачем-то';
		$search_ignore[] = 'затем';
		$search_ignore[] = 'затем-то';
		$search_ignore[] = 'захотел';
		$search_ignore[] = 'захотела';
		$search_ignore[] = 'захотели';
		$search_ignore[] = 'захочу';
		$search_ignore[] = 'знает';
		$search_ignore[] = 'знал';
		$search_ignore[] = 'знала';
		$search_ignore[] = 'знали';
		$search_ignore[] = 'знало';
		$search_ignore[] = 'знаю';
		$search_ignore[] = 'и';
		$search_ignore[] = 'и т.д';
		$search_ignore[] = 'и т.п';
		$search_ignore[] = 'ибо';
		$search_ignore[] = 'из';
		$search_ignore[] = 'из-под';
		$search_ignore[] = 'или';
		$search_ignore[] = 'иначе';
		$search_ignore[] = 'иногда';
		$search_ignore[] = 'искал';
		$search_ignore[] = 'искала';
		$search_ignore[] = 'искали';
		$search_ignore[] = 'искало';
		$search_ignore[] = 'искать';
		$search_ignore[] = 'им';
		$search_ignore[] = 'имхо';
		$search_ignore[] = 'итак';
		$search_ignore[] = 'их';
		$search_ignore[] = 'к';
		$search_ignore[] = 'каждый';
		$search_ignore[] = 'как';
		$search_ignore[] = 'какая';
		$search_ignore[] = 'какие';
		$search_ignore[] = 'каким';
		$search_ignore[] = 'каких';
		$search_ignore[] = 'какого';
		$search_ignore[] = 'какое';
		$search_ignore[] = 'какой';
		$search_ignore[] = 'каком';
		$search_ignore[] = 'какому';
		$search_ignore[] = 'какую';
		$search_ignore[] = 'кем';
		$search_ignore[] = 'когда';
		$search_ignore[] = 'когда-нибудь';
		$search_ignore[] = 'кого';
		$search_ignore[] = 'ком';
		$search_ignore[] = 'кому';
		$search_ignore[] = 'которая';
		$search_ignore[] = 'которого';
		$search_ignore[] = 'которое';
		$search_ignore[] = 'которой';
		$search_ignore[] = 'котором';
		$search_ignore[] = 'которому';
		$search_ignore[] = 'которую';
		$search_ignore[] = 'которые';
		$search_ignore[] = 'который';
		$search_ignore[] = 'которым';
		$search_ignore[] = 'которых';
		$search_ignore[] = 'кто';
		$search_ignore[] = 'кто-то';
		$search_ignore[] = 'кстати';
		$search_ignore[] = 'куда';
		$search_ignore[] = 'ли';
		$search_ignore[] = 'либо';
		$search_ignore[] = 'лол';
		$search_ignore[] = 'лучшая';
		$search_ignore[] = 'лучшее';
		$search_ignore[] = 'лучшие';
		$search_ignore[] = 'лучший';
		$search_ignore[] = 'любая';
		$search_ignore[] = 'любого';
		$search_ignore[] = 'любое';
		$search_ignore[] = 'любой';
		$search_ignore[] = 'любому';
		$search_ignore[] = 'любую';
		$search_ignore[] = 'любые';
		$search_ignore[] = 'любым';
		$search_ignore[] = 'любыми';
		$search_ignore[] = 'любых';
		$search_ignore[] = 'маленькая';
		$search_ignore[] = 'маленький';
		$search_ignore[] = 'маленьким';
		$search_ignore[] = 'маленького';
		$search_ignore[] = 'маленькое';
		$search_ignore[] = 'маленькой';
		$search_ignore[] = 'маленьком';
		$search_ignore[] = 'маленькому';
		$search_ignore[] = 'маленькую';
		$search_ignore[] = 'между';
		$search_ignore[] = 'менее';
		$search_ignore[] = 'меньше';
		$search_ignore[] = 'меня';
		$search_ignore[] = 'мир';
		$search_ignore[] = 'мне';
		$search_ignore[] = 'много';
		$search_ignore[] = 'мной';
		$search_ignore[] = 'мог';
		$search_ignore[] = 'могла';
		$search_ignore[] = 'могли';
		$search_ignore[] = 'могло';
		$search_ignore[] = 'могу';
		$search_ignore[] = 'мое';
		$search_ignore[] = 'моё';
		$search_ignore[] = 'можем';
		$search_ignore[] = 'может';
		$search_ignore[] = 'мои';
		$search_ignore[] = 'мой';
		$search_ignore[] = 'моя';
		$search_ignore[] = 'мы';
		$search_ignore[] = 'на';
		$search_ignore[] = 'над';
		$search_ignore[] = 'назад';
		$search_ignore[] = 'наиболее';
		$search_ignore[] = 'наименее';
		$search_ignore[] = 'напишет';
		$search_ignore[] = 'напишу';
		$search_ignore[] = 'напишут';
		$search_ignore[] = 'например';
		$search_ignore[] = 'нас';
		$search_ignore[] = 'нам';
		$search_ignore[] = 'нами';
		$search_ignore[] = 'наш';
		$search_ignore[] = 'наша';
		$search_ignore[] = 'наше';
		$search_ignore[] = 'нашего';
		$search_ignore[] = 'нашей';
		$search_ignore[] = 'нашем';
		$search_ignore[] = 'нашему';
		$search_ignore[] = 'наши';
		$search_ignore[] = 'нашим';
		$search_ignore[] = 'нашими';
		$search_ignore[] = 'наших';
		$search_ignore[] = 'нашу';
		$search_ignore[] = 'не';
		$search_ignore[] = 'него';
		$search_ignore[] = 'недавно';
		$search_ignore[] = 'неужели';
		$search_ignore[] = 'нее';
		$search_ignore[] = 'неё';
		$search_ignore[] = 'немного';
		$search_ignore[] = 'нет';
		$search_ignore[] = 'ни';
		$search_ignore[] = 'никогда';
		$search_ignore[] = 'никто';
		$search_ignore[] = 'ничто';
		$search_ignore[] = 'ним';
		$search_ignore[] = 'ничего';
		$search_ignore[] = 'но';
		$search_ignore[] = 'новая';
		$search_ignore[] = 'нового';
		$search_ignore[] = 'новое';
		$search_ignore[] = 'новой';
		$search_ignore[] = 'новом';
		$search_ignore[] = 'новому';
		$search_ignore[] = 'новостей';
		$search_ignore[] = 'новости';
		$search_ignore[] = 'новость';
		$search_ignore[] = 'новостями';
		$search_ignore[] = 'новую';
		$search_ignore[] = 'новый';
		$search_ignore[] = 'новый';
		$search_ignore[] = 'новым';
		$search_ignore[] = 'новых';
		$search_ignore[] = 'новых';
		$search_ignore[] = 'ну';
		$search_ignore[] = 'нужен';
		$search_ignore[] = 'нужна';
		$search_ignore[] = 'нужно';
		$search_ignore[] = 'нужны';
		$search_ignore[] = 'о';
		$search_ignore[] = 'об';
		$search_ignore[] = 'однако';
		$search_ignore[] = 'около';
		$search_ignore[] = 'он';
		$search_ignore[] = 'она';
		$search_ignore[] = 'они';
		$search_ignore[] = 'оно';
		$search_ignore[] = 'от';
		$search_ignore[] = 'ответ';
		$search_ignore[] = 'ответил';
		$search_ignore[] = 'ответила';
		$search_ignore[] = 'ответили';
		$search_ignore[] = 'ответило';
		$search_ignore[] = 'откуда';
		$search_ignore[] = 'отнюдь';
		$search_ignore[] = 'перед';
		$search_ignore[] = 'писал';
		$search_ignore[] = 'писала';
		$search_ignore[] = 'писали';
		$search_ignore[] = 'писало';
		$search_ignore[] = 'пишет';
		$search_ignore[] = 'пишу';
		$search_ignore[] = 'пишут';
		$search_ignore[] = 'плохо';
		$search_ignore[] = 'по';
		$search_ignore[] = 'поблизости';
		$search_ignore[] = 'под';
		$search_ignore[] = 'пожалуйста';
		$search_ignore[] = 'получала';
		$search_ignore[] = 'получали';
		$search_ignore[] = 'получало';
		$search_ignore[] = 'получать';
		$search_ignore[] = 'получил';
		$search_ignore[] = 'получила';
		$search_ignore[] = 'получили';
		$search_ignore[] = 'получило';
		$search_ignore[] = 'получить';
		$search_ignore[] = 'пор';
		$search_ignore[] = 'пора';
		$search_ignore[] = 'порам';
		$search_ignore[] = 'порами';
		$search_ignore[] = 'поре';
		$search_ignore[] = 'порой';
		$search_ignore[] = 'пору';
		$search_ignore[] = 'поры';
		$search_ignore[] = 'после';
		$search_ignore[] = 'потому';
		$search_ignore[] = 'почему';
		$search_ignore[] = 'почти';
		$search_ignore[] = 'пошли';
		$search_ignore[] = 'правда';
		$search_ignore[] = 'прежде';
		$search_ignore[] = 'примерно';
		$search_ignore[] = 'про';
		$search_ignore[] = 'просто';
		$search_ignore[] = 'прошел';
		$search_ignore[] = 'прошёл';
		$search_ignore[] = 'прошла';
		$search_ignore[] = 'прошли';
		$search_ignore[] = 'прошло';
		$search_ignore[] = 'пускай';
		$search_ignore[] = 'пусть';
		$search_ignore[] = 'раз';
		$search_ignore[] = 'раза';
		$search_ignore[] = 'разве';
		$search_ignore[] = 'реже';
		$search_ignore[] = 'с';
		$search_ignore[] = 'сам';
		$search_ignore[] = 'сама';
		$search_ignore[] = 'сами';
		$search_ignore[] = 'самим';
		$search_ignore[] = 'самими';
		$search_ignore[] = 'самих';
		$search_ignore[] = 'само';
		$search_ignore[] = 'самого';
		$search_ignore[] = 'самое';
		$search_ignore[] = 'самой';
		$search_ignore[] = 'самом';
		$search_ignore[] = 'самому';
		$search_ignore[] = 'самая';
		$search_ignore[] = 'саму';
		$search_ignore[] = 'самую';
		$search_ignore[] = 'самые';
		$search_ignore[] = 'самый';
		$search_ignore[] = 'самым';
		$search_ignore[] = 'самыми';
		$search_ignore[] = 'самых';
		$search_ignore[] = 'сделал';
		$search_ignore[] = 'сделала';
		$search_ignore[] = 'сделали';
		$search_ignore[] = 'сделало';
		$search_ignore[] = 'сейчас';
		$search_ignore[] = 'сквозь';
		$search_ignore[] = 'скорее';
		$search_ignore[] = 'скоро';
		$search_ignore[] = 'случае';
		$search_ignore[] = 'случаем';
		$search_ignore[] = 'случай';
		$search_ignore[] = 'случаю';
		$search_ignore[] = 'случая';
		$search_ignore[] = 'смотря';
		$search_ignore[] = 'со';
		$search_ignore[] = 'совсем';
		$search_ignore[] = 'спасибо';
		$search_ignore[] = 'старая';
		$search_ignore[] = 'старого';
		$search_ignore[] = 'старое';
		$search_ignore[] = 'старой';
		$search_ignore[] = 'старом';
		$search_ignore[] = 'старому';
		$search_ignore[] = 'старую';
		$search_ignore[] = 'старый';
		$search_ignore[] = 'старым';
		$search_ignore[] = 'старых';
		$search_ignore[] = 'так';
		$search_ignore[] = 'также';
		$search_ignore[] = 'твое';
		$search_ignore[] = 'твоего';
		$search_ignore[] = 'твоё';
		$search_ignore[] = 'твои';
		$search_ignore[] = 'твой';
		$search_ignore[] = 'твою';
		$search_ignore[] = 'твоя';
		$search_ignore[] = 'те';
		$search_ignore[] = 'тебя';
		$search_ignore[] = 'тем';
		$search_ignore[] = 'теми';
		$search_ignore[] = 'теперь';
		$search_ignore[] = 'тех';
		$search_ignore[] = 'тобой';
		$search_ignore[] = 'тогда';
		$search_ignore[] = 'того';
		$search_ignore[] = 'тоже';
		$search_ignore[] = 'той';
		$search_ignore[] = 'только';
		$search_ignore[] = 'туда';
		$search_ignore[] = 'у';
		$search_ignore[] = 'ужасно';
		$search_ignore[] = 'уже';
		$search_ignore[] = 'узнает';
		$search_ignore[] = 'узнают';
		$search_ignore[] = 'ушел';
		$search_ignore[] = 'ушёл';
		$search_ignore[] = 'ушла';
		$search_ignore[] = 'ушли';
		$search_ignore[] = 'ушло';
		$search_ignore[] = 'ходил';
		$search_ignore[] = 'ходила';
		$search_ignore[] = 'ходили';
		$search_ignore[] = 'ходило';
		$search_ignore[] = 'ходит';
		$search_ignore[] = 'ходить';
		$search_ignore[] = 'ходят';
		$search_ignore[] = 'хорошо';
		$search_ignore[] = 'хотел';
		$search_ignore[] = 'хотела';
		$search_ignore[] = 'хотели';
		$search_ignore[] = 'хотело';
		$search_ignore[] = 'хотя';
		$search_ignore[] = 'хочу';
		$search_ignore[] = 'чаще';
		$search_ignore[] = 'чего';
		$search_ignore[] = 'чем';
		$search_ignore[] = 'чём';
		$search_ignore[] = 'чем-то';
		$search_ignore[] = 'чём-то';
		$search_ignore[] = 'чему';
		$search_ignore[] = 'через';
		$search_ignore[] = 'что';
		$search_ignore[] = 'чтоб';
		$search_ignore[] = 'чтобы';
		$search_ignore[] = 'чье';
		$search_ignore[] = 'чьё';
		$search_ignore[] = 'чьей';
		$search_ignore[] = 'чьему';
		$search_ignore[] = 'чьи';
		$search_ignore[] = 'чьим';
		$search_ignore[] = 'чьими';
		$search_ignore[] = 'чьих';
		$search_ignore[] = 'чья';
		$search_ignore[] = 'шел';
		$search_ignore[] = 'шёл';
		$search_ignore[] = 'шла';
		$search_ignore[] = 'шли';
		$search_ignore[] = 'шло';
		$search_ignore[] = 'эта';
		$search_ignore[] = 'эти';
		$search_ignore[] = 'этим';
		$search_ignore[] = 'этих';
		$search_ignore[] = 'это';
		$search_ignore[] = 'этой';
		$search_ignore[] = 'этому';
		$search_ignore[] = 'этот';
		$search_ignore[] = 'эту';
		$search_ignore[] = 'я';
		$search_ignore[] = 'является';
		$search_ignore[] = 'являются';

		return $search_ignore;
	}

	/**
	 * Returns the lower length limit of search words
	 *
	 * @return	integer  The lower length limit of search words.
	 * @since	1.6
	 */
	public static function getLowerLimitSearchWord()
	{
		return 3;
	}

	/**
	 * Returns the upper length limit of search words
	 *
	 * @return	integer  The upper length limit of search words.
	 * @since	1.6
	 */
	public static function getUpperLimitSearchWord()
	{
		return 20;
	}

	/**
	 * Returns the number of chars to display when searching
	 *
	 * @return	integer  The number of chars to display when searching.
	 * @since	1.6
	 */
	public static function getSearchDisplayedCharactersNumber()
	{
		return 200;
	}
}

