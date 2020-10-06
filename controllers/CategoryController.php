<?php


namespace app\controllers;
use app\models\Category;
use app\models\Product;
use Yii;
use yii\data\Pagination;
use yii\web\HttpException;

class CategoryController extends AppController
{
    public function actionIndex()
    {
        $hits = Product::find()->where(['hit' => '1'])->limit(6)->all();
        $this->setMeta('INTER-SHOP');
        return $this->render('index', compact('hits'));
    }

    public function actionView($id)
    {
        //$id = Yii::$app->request->get('id');

        $category = Category::findOne($id);
        if(empty($category)) throw new HttpException(404, 'Такой категории пока нету..');
//        $products = Product::find()->where(['category_id' => $id])->all();
        $query = Product::find()->where(['category_id' => $id]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 12,
            'forcePageParam' => false, 'pageSizeParam' => false]);
        $products = $query->offset($pages->offset)->limit($pages->limit)->all();
        $this->setMeta('INTER-SHOP | ' . $category->name, $category->keywords, $category->description);
        return $this->render('view', compact('products','pages','category'));
    }

    public function actionSearch()
    {
        $search = trim(Yii::$app->request->get('search'));
        $this->setMeta('INTER-SHOP | ' . 'Поиск: '. $search);
//        if(!$search){
//            return $this->render('search');
//        }
        $query = Product::find()->where(['like', 'name', $search]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 12,
            'forcePageParam' => false, 'pageSizeParam' => false]);
        $products = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('search', compact('products', 'pages', 'search'));
    }
}