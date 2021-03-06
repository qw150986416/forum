<?php

namespace app\controllers;

use Yii;
use app\models\Node;
use app\models\search\NodeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\VerbFilter;

/**
 * NodeController implements the CRUD actions for Node model.
 */
class NodeController extends Controller
{
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * Lists all Node models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new NodeSearch;
		$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel,
		]);
	}

	/**
	 * Displays a single Node model.
	 * @param string $id
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new Node model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new Node;
		if ($model->load(Yii::$app->request->post())) {
			$post=Yii::$app->request->post();
			$parent_node = $post['node']['parent'];
	        if ($parent_node != 0) {
	            $node = Node::find($parent_node);
	            $model->appendTo($node);
	            return $this->redirect(['view', 'id' => $model->id]);
	        }
	        if ($model->saveNode()) {
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}
        
		return $this->render('create', [
			'model' => $model,
		]);
	}

	/**
	 * Updates an existing Node model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param string $id
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ($model->load(Yii::$app->request->post())) {
			$post=Yii::$app->request->post();
			$parent_node = $post['node']['parent'];
	        if ($parent_node != 0) {
	            $node = Node::find($parent_node);
                $parent=$model->parent()->one();
                if ($node->id !== $model->id && $node->id !== $parent->id) {
                    $model->moveAsLast($node);
                }
	        }
	        if ($model->saveNode()) {
				return $this->redirect(['view', 'id' => $model->id]);
			}
		}
        
		return $this->render('update', [
			'model' => $model,
		]);

		if ($model->load(Yii::$app->request->post()) && $model->saveNode()) {
			return $this->redirect(['view', 'id' => $model->id]);
		} else {
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Node model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param string $id
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();
		return $this->redirect(['index']);
	}

	/**
	 * Finds the Node model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param string $id
	 * @return Node the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if ($id !== null && ($model = Node::find($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
