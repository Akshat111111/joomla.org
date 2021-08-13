<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

require_once JPATH_THEMES . '/cassiopeia/Helper/Template.php';

$template = Factory::getApplication()->getTemplate(true);
$tparams = $template->params ?? new Registry;

$language = Factory::getLanguage()->getTag();

HTMLHelper::_('bootstrap.collapse');

?>

<div class="container">

    <nav class="navbar navbar-expand-md">
        <button class="ms-auto my-2 navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse" data-bs-target="#navbar<?php echo $module->id; ?>" aria-controls="navbar<?php echo $module->id; ?>" aria-expanded="false" aria-label="<?php echo Text::_('MOD_MENU_TOGGLE'); ?>">
            <span class="icon-menu" aria-hidden="true"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar<?php echo $module->id; ?>">

        <?php echo JoomlaTemplateHelper::getTemplateMenu($language, (bool) $tparams->get('useCdn', '1')); ?>

        </div>

    </nav>

</div>
