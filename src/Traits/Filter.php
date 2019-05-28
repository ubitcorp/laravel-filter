<?php

namespace ubitcorp\Filter\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait Filter
{
 
  public function scopeFilter(Builder $query, $params=[], $ignoreNull = true, $ignoreFields=[])
  {   
      if(!count($params))
      {
          $params = request()->all();
      }

      $preIgnoredFields = config("filter.pre_ignored_fields",[]);

      if(isset($params["fields"]))  //return back only this fields
      {
        $fields = array_map('trim',explode(",", str_replace("|"," as ",$params["fields"]))); 
        
        $query->select($fields);              
      }

      foreach($params as $key=>$val)
      {
          if(in_array($key, $ignoreFields) || in_array($key, $preIgnoredFields))
              continue; 
          if(!$ignoreNull || ($ignoreNull && !is_null($val))){
              if(strpos($key,"|"))//alt sorgu
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
              else if(is_array($val)){
                  if(array_key_exists("_json_", $val))
                  {
                    $query->whereRaw('json_contains('.$key.', \'' . json_encode($val['_json_'],JSON_UNESCAPED_UNICODE) . '\')');
                  }
                  else
                      $query->whereIn($key, $val);
              }else if(strpos($key, "_id"))
                  $query->where($key,$val);
              //else if(isset($this->casts[$key]) && $this->casts[$key]=="json")            
                  //$query->whereJsonContains($key,[$val]);
              else
                  $query->where($key,'like','%'.$val.'%');
              
          }
      }

      //sort = -name --> desc, name -> asc
      if(isset($params['sort'])){
        
        $way = 'ASC';
        if(substr($params['sort'],0,1)=='-')
        {
          $way = 'DESC';
          $params['sort']=str_replace('-','',$params['sort']);
        }
        
        $query->orderBy($params['sort'], $way);
      }
      
      return $query;
  }
}
