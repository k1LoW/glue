<?php
class GlueBehavior extends ModelBehavior {

    private $forceSetPrimaryKey;

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
     * beforeFind
     *
     * @param &$model, $data
     */
    public function beforeFind(&$model, $query){
        $this->forceSetPrimaryKey = false;
        if (!isset($model->hasGlued) || empty($query['fields'])) {
            return $query;
        }
        $schema = $model->_schema;
        foreach ($query['fields'] as $key => $field) {
            if (!in_array(preg_replace('/' . $model->alias . '\./' , '', $field), array_keys($schema))
                && !in_array($field, array_keys($schema))) {

                if (!in_array($model->alias . '.' . $model->primaryKey, $query['fields'])
                    && !in_array($model->alias . '.' . $model->primaryKey, $query['fields'])) {
                    $query['fields'][] = $model->alias . '.' . $model->primaryKey;
                    $this->forceSetPrimaryKey = true;
                }

                foreach ($model->hasGlued as $gluedModelName => $params) {
                    $gluedSchema = $model->{$gluedModelName}->_schema;
                    if (in_array(preg_replace('/^' . $model->alias . '\./' , '', $field), array_keys($gluedSchema))
                        || in_array($field, array_keys($gluedSchema))) {
                        $query['fields'][] = $gluedModelName . '.' . $params['foreignKey'];
                        $query['fields'][] = $gluedModelName . '.' . preg_replace('/^' . $model->alias . '\./' , '', $field);
                        unset($query['fields'][$key]);
                    }
                }
            }
        }

        return $query;
    }

    /**
     * afterFind
     *
     * @param &$model, $results
     */
    public function afterFind(&$model, $results){
        return $this->glueAfterFind($model, $results);
    }

    /**
     * glueAfterFind
     *
     * @param &$model, $results
     */
    public function glueAfterFind(&$model, $results){
        if (!$results) {
            return $results;
        }
        if (isset($model->hasGlued)) {
            foreach ($results as $key => $value) {
                foreach ($model->hasGlued as $gluedModelName => $params) {
                    if (!empty($value[$model->alias]) && !empty($value[$gluedModelName])) {
                        unset($value[$gluedModelName][$model->primaryKey]);
                        unset($value[$gluedModelName][$params['foreignKey']]);
                        unset($value[$gluedModelName]['created']);
                        unset($value[$gluedModelName]['modified']);
                        // give priority to master model fields
                        $results[$key][$model->alias] = Set::merge($value[$gluedModelName],$results[$key][$model->alias]);

                        if($this->forceSetPrimaryKey) {
                            unset($results[$key][$model->alias][$model->primaryKey]);
                        }

                        unset($results[$key][$gluedModelName]);
                    }
                }
            }
        }
        if (isset($model->hasMany)) {
            foreach ($model->hasMany as $modelName => $params) {
                if (isset($model->{$modelName}->hasGlued)) {
                    foreach ($results as $key => $value) {
                        if (empty($results[$key][$modelName])) {
                            continue;
                        }
                        $ids = Set::extract('/' . $model->{$modelName}->primaryKey, $results[$key][$modelName]);
                        $gluedResults = $model->{$modelName}->find('all', array('conditions' => array($model->{$modelName}->alias . '.' . $model->{$modelName}->primaryKey => $ids),
                                                                                'recursive' => 0));
                        $gluedResults = Set::extract('/' . $model->{$modelName}->alias . '/.', $gluedResults);
                        $results[$key][$modelName] = Set::merge($gluedResults, $results[$key][$modelName]);
                        foreach ($model->{$modelName}->hasGlued as $gluedModelName => $params2) {
                            foreach ($results[$key][$modelName] as $key2 => $value2) {
                                unset($results[$key][$modelName][$key2][$gluedModelName]);
                            }
                        }
                    }
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
        if (!isset($model->hasGlued)) {
            return;
        }
        if ($created) {
            $id = $model->getLastInsertId();
        } else {
            $id = $model->data[$model->alias][$model->primaryKey];
        }
        foreach ($model->hasGlued as $gluedModelName => $params) {
            $gluedSchema = $model->{$gluedModelName}->_schema;
            unset($gluedSchema[$model->{$gluedModelName}->primaryKey]);
            unset($gluedSchema['created']);
            unset($gluedSchema['modified']);
            $data = array();
            $data[$gluedModelName] = array();
            foreach ($model->data[$model->alias] as $key => $value) {
                if (in_array($key, array_keys($gluedSchema))) {
                    $data[$gluedModelName][$key] = $value;
                }
            }
            $data[$gluedModelName][$params['foreignKey']] = $id;
            $current = $model->{$gluedModelName}->find('first', array('conditions' => array($gluedModelName . '.' . $params['foreignKey'] => $id)));
            if ($current) {
                $data = Set::merge($current, $data);
                unset($data[$gluedModelName]['modified']);
                unset($data[$gluedModelName]['created']);
            } else {
                $model->{$gluedModelName}->create($data);
            }

            $model->{$gluedModelName}->save($data);
        }
    }

}