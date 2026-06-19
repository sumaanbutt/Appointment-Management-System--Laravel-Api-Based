<?php


namespace App\Helpers;

class ApiDataHelper
{
    public static function process($query, $request)
    {
        $dataCategory = $request->input('data_category', 'paginate');
        $limit = $request->input('limit', 10);
        $orderBy = $request->input('orderBy', 'code');
        $orderType = $request->input('orderType', 'desc');
        $select = $request->input('select', '*');

        if ($select !== '*'){
            $query->select(
                array_map('trim', explode(',', $select))
            );
        }

        $query->orderBy(
            $orderBy,
            $orderType
        );

        switch ($dataCategory) {

            case 'list':
                return $query->get();

            case 'stats':
                return [
                    'total' => $query->count()
                ];

            default:
                    return $query->paginate($limit);
        }
    }
}
