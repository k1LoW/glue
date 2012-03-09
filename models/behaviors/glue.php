<?php
class GlueBehavior extends ModelBehavior {

    /**
     * setUp
     *
     * @param &$model
     * @param $config
     */
    public function setUp(&$model, $config = array()){
        $this->__glue($model);
    }

    /**
     * __glue
     *
     * @param &$model
     */
    private function __glue(&$model){
        if (!isset($model->hasGlued)) {
            return;
        }
        foreach ($model->hasGlued as $gluedModelName => $params) {
            if (empty($params['foreignKey'])) {
                $foreignKey = Inflector::underscore($model->alias) . '_id';
                $params['foreignKey'] = $foreignKey;
                $model->hasGlued[$gluedModelName]['foreignKey'] = $foreignKey;
            }
            $model->bindModel(array('hasOne' => array(
                                                            $gluedModelName => $params
                                                            ))
                                    , false);
        }
    }

    /**
     * afterFind
     *
     * @param &$model, $results
     */
    public function afterFind(&$model, $results){
        foreach ($results as $key => $value) {
            foreach ($model->hasGlued as $gluedModelName => $params) {
                if (!empty($value[$model->alias]) && !empty($value[$gluedModelName])) {
                    unset($value[$gluedModelName][$model->primaryKey]);
                    unset($value[$gluedModelName][$params['foreignKey']]);
                    unset($value[$gluedModelName]['created']);
                    unset($value[$gluedModelName]['modified']);
                    // give priority to master model fields
                    $results[$key][$model->alias] = Set::merge($value[$gluedModelName],$results[$key][$model->alias]);
                    unset($results[$key][$gluedModelName]);
                }
            }
        }
        return $results;
    }

    /**
     * afterSave
     *
     * @param &$model, $created
     */
    public function afterSave(&$model, $created){
        if ($created) {
            $id = $model->getLastInsertId();
        } else {
            $id = $model->data[$model->alias][$model->primaryKey];
        }
        foreach ($model->hasGlued as $gluedModelName => $params) {
            $schema = $model->{$gluedModelName}->_schema;
            unset($schema[$model->{$gluedModelName}->primaryKey]);
            unset($schema['created']);
            unset($schema['modified']);
            $data = array();
            $data[$gluedModelName] = array();
            foreach ($model->data[$model->alias] as $key => $value) {
                if (in_array($key, array_keys($schema))) {
                    $data[$gluedModelName][$key] = $value;
                }
            }
            $data[$gluedModelName][$params['foreignKey']] = $id;
            $current = $model->{$gluedModelName}->find('first', array('conditions' => array($gluedModelName . '.' . $params['foreignKey'] => $id)));
            if ($current) {
                $data = Set::merge($current, $data);
            }

            $model->{$gluedModelName}->create();
            $model->{$gluedModelName}->set($data);
            $model->{$gluedModelName}->save($data);
        }
    }

}