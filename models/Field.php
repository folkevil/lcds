<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "field".
 *
 * @property int $id
 * @property int $template_id
 * @property float $x1
 * @property float $y1
 * @property float $x2
 * @property float $y2
 * @property string $css
 * @property string $js
 * @property string $append_params
 * @property ScreenTemplate $template
 * @property FieldHasContentType[] $fieldHasContentTypes
 * @property ContentType[] $contentTypes
 */
class Field extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['template_id', 'x1', 'y1', 'x2', 'y2'], 'required'],
            [['template_id'], 'integer'],
            [['x1', 'y1', 'x2', 'y2'], 'number'],
            [['css', 'js'], 'string'],
            [['append_params'], 'string', 'max' => 1024],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScreenTemplate::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'template_id' => Yii::t('app', 'Template ID'),
            'x1' => Yii::t('app', 'X1'),
            'y1' => Yii::t('app', 'Y1'),
            'x2' => Yii::t('app', 'X2'),
            'y2' => Yii::t('app', 'Y2'),
            'css' => Yii::t('app', 'Css'),
            'js' => Yii::t('app', 'Js'),
            'append_params' => Yii::t('app', 'Append Params'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate()
    {
        return $this->hasOne(ScreenTemplate::className(), ['id' => 'template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldHasContentTypes()
    {
        return $this->hasMany(FieldHasContentType::className(), ['field_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentTypes()
    {
        return $this->hasMany(ContentType::className(), ['id' => 'content_type_id'])->viaTable('field_has_content_type', ['field_id' => 'id']);
    }
}