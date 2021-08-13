<?php
/**
 * Joomla.org site template
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Helper class for the Joomla template
 */
class JoomlaTemplateHelper
{
	/**
	 * Retrieve the "report an issue" link for the current site
	 *
	 * Note that this helper method is only 'good' for the live site, for development environments it will use a default link
	 *
	 * @param   string  $siteUrl  The site URL without the scheme
	 *
	 * @return  string  The issue link
	 */
	public static function getIssueLink($siteUrl)
	{
		$hasCustom = false;

		switch ($siteUrl)
		{
			case 'api.joomla.org':
			{
				$tag = 'japi';

				break;
			}

			case 'certification.joomla.org':
			{
				$tag = 'jcertif';

				break;
			}

			case 'community.joomla.org':
			{
				$tag = 'jcomm';

				break;
			}

			case 'conference.joomla.org':
			{
				$tag = 'jconf';

				break;
			}

			case 'developer.joomla.org':
			{
				$tag = 'jdev';

				break;
			}

			case 'docs.joomla.org':
			{
				$tag = 'jdocs';

				break;
			}

			case 'domains.joomla.org':
			{
				$tag = 'jdomain';

				break;
			}

			case 'downloads.joomla.org':
			{
				$tag = 'jdown';

				break;
			}

			case 'extensions.joomla.org':
			{
				$hasCustom = true;
				$tag       = 'jed';
				$url       = 'https://github.com/joomla/jed-issues/issues/new?body=Please%20describe%20the%20problem%20or%20your%20issue';

				break;
			}

			case 'forum.joomla.org':
			{
				$tag = 'jforum';

				break;
			}

			case 'joomlafoundation.org':
			{
				$tag = 'jfoundation';

				break;
			}

			case 'framework.joomla.org':
			{
				$hasCustom = true;
				$tag       = 'jfw';
				$url       = 'https://github.com/joomla/framework.joomla.org/issues/new?title=[FW%20Site]&body=Please%20state%20the%20nature%20of%20your%20development%20emergency';

				break;
			}

			case 'issues.joomla.org':
			{
				$hasCustom = true;
				$tag       = 'jissues';
				$url       = 'https://issues.joomla.org/tracker/jtracker';

				break;
			}

			case 'magazine.joomla.org':
			{
				$tag = 'jcm';

				break;
			}

			case 'opensourcematters.org':
			{
				$tag = 'josm';

				break;
			}

			case 'showcase.joomla.org':
			{
				$tag = 'jshow';

				break;
			}

			case 'tm.joomla.org':
			{
				$tag = 'jtm';

				break;
			}
			
			case 'volunteers.joomla.org':
			{
				$hasCustom = true;
				$tag       = 'jvols';
				$url       = 'https://github.com/joomla/volunteers.joomla.org/issues/new?body=Please%20describe%20the%20problem%20or%20your%20issue';

				break;
			}

			case 'www.joomla.org':
			{
				$tag = 'joomla.org';

				break;
			}

			default:
				$tag = '';

				break;
		}

		// Build the URL if we aren't using a custom source
		if (!$hasCustom)
		{
			$url = 'https://github.com/joomla/joomla-websites/issues/new?';

			// Do we have a tag?
			if (!empty($tag))
			{
				$url .= "title=[$tag]%20&";
			}

			$url .= 'body=Please%20describe%20the%20problem%20or%20your%20issue';
		}

		return $url;
	}

	/**
	 * Load the template's footer section
	 *
	 * @param   string   $lang    The language to request
	 * @param   boolean  $useCdn  True to load resource from the cdn, false from local instance
	 *
	 * @return  string
	 */
	public static function getTemplateFooter($lang, $useCdn = true)
	{
		$result = self::loadTemplateSection('footer', $lang, $useCdn);

		// Check for an error
		if ($result === 'Could not load template section.')
		{
			return $result;
		}

		// Replace the placeholders and return the result
		return strtr(
			$result,
			[
				'%reportroute%' => static::getIssueLink(Uri::getInstance()->toString(['host'])),
				'%currentyear%' => date('Y'),
			]
		);
	}

	/**
	 * Load the template's CDN menu section
	 *
	 * @param   string   $lang    The language to request
	 * @param   boolean  $useCdn  True to load resource from the cdn, false from local instance
	 *
	 * @return  string
	 */
	public static function getTemplateMenu($lang, $useCdn = true)
	{
		return self::loadTemplateSection('menu', $lang, $useCdn);
	}

	/**
	 * Load the template section, caching the result if needed
	 *
	 * @param   string   $section  The section to be loaded
	 * @param   string   $lang     The language to request
	 * @param   boolean  $useCdn   True to load resource from the cdn, false from local instance
	 *
	 * @return  string
	 */
	private static function loadTemplateSection($section, $lang, $useCdn = true)
	{
		if (JDEBUG || !$useCdn)
		{
			$path = dirname(__DIR__) . "/cdn/layouts/$section/$lang.$section.html";

			if (!file_exists($path))
			{
				$path = dirname(__DIR__) . "/cdn/layouts/$section/en-GB.$section.html";
			}

			return file_get_contents($path);
		}

		/** @var \Joomla\CMS\Cache\Controller\CallbackController $cache */
		$cache = Factory::getCache('cassiopeia', 'callback');

		// This is always cached regardless of the site's global setting
		$cache->setCaching(true);

		// Cache this for one day
		$cache->setLifeTime(1440);

		// Build the remote URL
		$url = "https://cdn.joomla.org/template/j4/renderer.php?section=$section&language=$lang";

		try
		{
			return $cache->get(
				function ($url)
				{
					// Set a very short timeout to try and not bring the site down
					$response = HttpFactory::getHttp()->get($url, [], 2);

					if ($response->code !== 200)
					{
						throw new RuntimeException('Could not load template section.');
					}

					$body = $response->body;

					$search = [
						'pull-right'
					];
					$replace = [
						'float-end'
					];

					return $body;
				},
				[$url],
				md5(__METHOD__ . $section . $lang)
			);
		}
		catch (RuntimeException $e)
		{
			return 'Could not load template section.';
		}
	}

}
