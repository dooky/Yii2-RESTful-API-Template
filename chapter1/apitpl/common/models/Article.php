<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $category_id
 * @property integer $status
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Adminuser $createdBy
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * 文章状态
     */
    
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 10;
    
    public static function allStatus()
    {
        return [self::STATUS_DRAFT=>'草稿',self::STATUS_PUBLISHED=>'已发布'];
    }
    
    public function getStatusStr()
    {
        return $this->status==self::STATUS_DRAFT?'草稿':'已发布';
    }
    
    /**
     * 文章分类
     */
    
    private static $cateStrArray = [ 1=>'静态页面',
                                2=>'网站公告',
                                3=>'行业新闻'];
    
    public static function allCategory()
    {
        return self::$cateStrArray;
    }
    
    public function getCateStr()
    {
        return self::$cateStrArray[$this->category_id];
    }
    
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content'], 'string'],
            [['category_id', 'status', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 512],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Adminuser::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'category_id' => '分类',
            'status' => '状态',
            'created_by' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '最后修改时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'created_by']);
    }
    
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            if($insert)
            {
                $this->created_at = time();
                $this->updated_at = time();
                $this->created_by = Yii::$app->user->identity->id;
            }
            else
            {
                $this->updated_at = time();
            }
            
            return true;
            
        }
        else
        {
            return false;
        }
    } 
    
}
