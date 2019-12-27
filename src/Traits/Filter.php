<?php

namespace ubitcorp\Filter\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use DB;

trait Filter
{
 
  public function scopeFilter(Builder $query, $params=[], $ignoreNull = true, $ignoreFields=[])
  {   
    if(env('UBITCORP_FILTER_LOG_SQL'))
      DB::enableQueryLog(); // Enable query log


      if(!count($params))
      {
          $params = request()->all();
      }     
 
      info($params);

      $preIgnoredFields = config("filter.pre_ignored_fields",[]);

      if(isset($params["fields"]))  //return back only this fields
      {
        $fields = array_map('trim',explode(",", str_replace("|"," as ",$params["fields"]))); 
        
        $query->select($fields);              
      }

      foreach($params as $key=>$val)
      {  
          //relation.column type filtering only works if that subqueary joined the main. old method is better than this.
          //we use -> for json properties, so can't replace > char. we should use . directly
          //$key = str_replace(">",".", $key);  //if relation already loaded
          
          if(in_array($key, $ignoreFields) || in_array($key, $preIgnoredFields))
              continue; 

          if(!$ignoreNull || ($ignoreNull && !is_null($val))){
            
              if(strpos($key,"|"))  //relations query
              {  
                  $str = explode("|", $key);
                 
                  $rel = $str[0];
                  $subkey = $str[1];
                  $query->whereHas($rel,function($query) use ($subkey, $val){ 
                      if(is_array($val))
                          $query->whereIn($subkey, $val);
                      else if(strpos($subkey, "_id"))
                          $query->where($subkey, $val);
                      else
                          $query->where($subkey, "like", '%'.$val.'%');
                  });
              }
              /*else if(is_array($val)){
                if(array_key_exists("_json_", $val))
                {
                  $query->whereRaw('json_contains('.$key.', \'' . json_encode($val['_json_'],JSON_UNESCAPED_UNICODE) . '\')');
                }
                else if(isset($this->casts[$key]) && $this->casts[$key]=="json")
                    $query->whereRaw('json_contains('.$key.', \'' . json_encode($val,JSON_UNESCAPED_UNICODE) . '\')');
                else
                    $query->whereIn($key, $val);
              } */             
              else if(isset($this->casts[$key]) && $this->casts[$key]=="json"){
                
                if(is_array($val))
                    $query->whereJsonContains($key, $val);
                else
                    $query->whereJsonContains($key, [$val]);                    
              }
              else if(is_array($val))
                $query->whereIn($key,$val);
              else if(strpos($key, "_id"))
                  $query->where($key, $val);           
              else
                  $query->where($key,'like','%'.$val.'%');
              
          }
      }

      //sort = -name --> desc, name -> asc
      //sort[]=nickname&sort[]=-created_at
      if(isset($params['sort'])){
        
        if(!is_array($params['sort']))
          $params['sort']=[$params['sort']];

        foreach($params['sort'] as $sort){
          $way = 'ASC';
          if(substr($sort,0,1)=='-')
          {
            $way = 'DESC';
            $sort=str_replace('-','',$sort);
          }
          
          $query->orderBy($sort, $way);
        }
      } 

      if(env('UBITCORP_FILTER_LOG_SQL')){
        info($query->toSql()); 
        info($query->getBindings());
      }

      return $query;
  }
}
