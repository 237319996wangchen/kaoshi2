<?php

namespace app\models;


class Guan extends TimeModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%guan}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sort', 'course_id'], 'required'],
            [['sort', 'created_at', 'updated_at', 'subject_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '章节分类ID',
            'name' => '章节分类名称',
            'sort' => '排序',
            'course_id' => '所属科目',
            'created_at' => '添加时间',
            'updated_at' => '修改时间',
        ];
    }
}
