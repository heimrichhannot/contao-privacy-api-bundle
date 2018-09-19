<?php

namespace HeimrichHannot\PrivacyApiBundle\Util;

use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\ApiBundle\Model\ApiAppModel;

class PrivacyApiUtil implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    public function addAppCategoriesToModel(Model $model, ApiAppModel $app)
    {
        if (null !== $model && $app->privacyAddAppCategoryToReferenceEntity && $app->categories)
        {
            $categoryField = $app->privacyReferenceCategoryField;
            $categories = StringUtil::deserialize($app->categories, true);

            if ($categoryField && $model->getTable() && !empty($categories))
            {
                \Controller::loadDataContainer($model->getTable());

                $dca = &$GLOBALS['TL_DCA'][$model->getTable()];
                $value = null;

                if (isset($dca['fields'][$categoryField]['eval']['multiple']) && $dca['fields'][$categoryField]['eval']['multiple'])
                {
                    $value = serialize($categories);
                }
                else
                {
                    $value = $categories[0];
                }

                $model->{$categoryField} = $value;
                $model->save();

                System::getContainer()->get('huh.categories.manager')->createAssociations(
                    $model->id, $categoryField, $model->getTable(), $categories
                );
            }
        }
    }
}