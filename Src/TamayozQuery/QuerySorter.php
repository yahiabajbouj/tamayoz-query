<?php

namespace Src\TamayozQuery;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Http\Request;
use InvalidArgumentException;

class QuerySorter
{

    protected $request;


    protected $builder;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * @param  $builder
     * @param array $sortable
     */
    public function apply($builder, array $sortable)
    {
        $this->builder = $builder;

        $attributes = $this->getAttr();

        // todo chain sort queries
        foreach ($attributes as $key => $value)
        {

            if(! in_array($key, $sortable))
                throw new InvalidArgumentException($key. ' is Unsortable field');

            if(! in_array(strtolower($value), ['asc', 'desc']))
                throw new InvalidArgumentException($value. ' is Unsupported type of sorting');

            if (preg_match('/\./', $key)) {
                $has = preg_replace("/\.\w+$/", '', $key);
                $key = preg_replace("/\w+\./", '', $key);

                // Todo not completed
                // supporting other relations orders
                if($this->builder instanceof HasManyThrough) {
                    $this->builder->with($has)->orderBy(
                        $this->builder->getParent()->getTable() . '.' . $key, $value
                    );
                }

            } else {
                $this->builder->orderBy($this->builder->getModel()->getTable() . '.'. $key, $value);
            }
        }

        return $this->builder;
    }



    /**
     * get wanted sort columns from request.
     * @return array
     */
    public function getAttr()
    {
        return isset($this->request['sort']) ? $this->request['sort'] : [];
    }

}
