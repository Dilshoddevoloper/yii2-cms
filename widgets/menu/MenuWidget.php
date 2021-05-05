<?php

namespace afzalroq\cms\widgets\menu;

use afzalroq\cms\entities\Collections;
use afzalroq\cms\entities\Entities;
use afzalroq\cms\entities\Items;
use afzalroq\cms\entities\MenuType;
use afzalroq\cms\entities\Options;
use slatiusa\nestable\Nestable;
use Yii;
use yii\base\UnknownPropertyException;
use yii\helpers\ArrayHelper;


class MenuWidget extends Nestable
{
    private $_uniqueItems = [];

    protected function renderItems($_items = NULL)
    {
        return '';
    }

    public function registerAssets() {
        return '';
    }

    public function run()
    {
        return '';
    }

    public function getMenu($slug = null)
    {
        if (MenuType::findOne(['slug' => $slug]))
            return $this->prepareItems(\afzalroq\cms\entities\Menu::find()->where(['menu_type_id' => MenuType::findOne(['slug' => $slug])->id])->orderBy('id'));

        throw new UnknownPropertyException(Yii::t('cms','Menu type not found').' : '.$slug);
    }

    protected function prepareItems($activeQuery): array
    {
        $langId = Yii::$app->params['cms']['languageIds'][Yii::$app->language];
        $items = [];
        foreach ($activeQuery->all() as $model) {
            if (in_array($model->id, $this->_uniqueItems)) {
                continue;
            } else {
                $this->_uniqueItems[] = $model->id;
                $name = ArrayHelper::getValue($this->modelOptions, 'title_' . $langId, 'title_' . $langId);
                $items[] = [
                    'id' => $model->getPrimaryKey(),
                    'content' => (is_callable($name) ? call_user_func($name, $model) : $model->{$name}),
                    'link' => $this->getLink($model),
                    'children' => $this->prepareItems($model->children(1)),
                ];
            }
        }
        return $items;
    }

    private static function getLink($model)
    {

        switch ($model->type) {
            case \afzalroq\cms\entities\Menu::TYPE_ACTION:
                $link = '/' . rtrim($model->type_helper, '/');
                break;
            case \afzalroq\cms\entities\Menu::TYPE_LINK:
                $link = mb_strtolower($model->type_helper);
                break;
            case \afzalroq\cms\entities\Menu::TYPE_COLLECTION:
                $collection = Collections::findOne($model->type_helper);
                $link = 'c/' . $collection->slug;
                break;
            case \afzalroq\cms\entities\Menu::TYPE_OPTION:
                $option = Options::findOne($model->type_helper);
                $link = 'c/' . $option->collection->slug . '/' . $option->slug;
                break;
            case \afzalroq\cms\entities\Menu::TYPE_ITEM:
                $item = Items::findOne($model->type_helper);
                $link = 'e/' . $item->entity->slug . '/' . $item->slug;
                break;
            case \afzalroq\cms\entities\Menu::TYPE_ENTITY:
                $entity = Entities::findOne($model->type_helper);
                $link = 'e/' . $entity->slug;
                break;
            case \afzalroq\cms\entities\Menu::TYPE_ENTITY_ITEM:
                $item = Items::findOne($model->type_helper);
                $link = 'e/' . $item->entity->slug . '/' . $item->slug;
                break;
            default:
                $link = '#';
        }

        return $link;
    }
}
