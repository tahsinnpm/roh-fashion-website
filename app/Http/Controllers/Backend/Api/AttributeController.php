<?php

namespace App\Http\Controllers\Backend\Api;

use App\Helpers\Classes\Combinations;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        $options = [];
        $choice_attributes = $request->get('choice_attributes', []);

        if (is_array($choice_attributes) && count($choice_attributes) > 0) {
            foreach ($choice_attributes as $key => $no) {
                $name = 'choice_options' . $no;
                $data = array();
                if ($request->has($name) && is_array($request[$name]) && count($request[$name]) > 0) {
                    foreach ($request[$name] as $key => $item) {
                        if ($item !== null && $item !== '') {
                            array_push($data, $item);
                        }
                    }
                    if (count($data) > 0) {
                        array_push($options, $data);
                    }
                }
            }
        }

        $data = [];
        if (count($options) > 0) {
            $combinations = Combinations::makeCombinations($options);
            foreach ($combinations as $combination) {
                $key = implode('-', $combination);
                if (!empty($key)) {
                    $data[$key] = rand(10, 100);
                }
            }
        }

        return response()->json(['success' => true, 'data' => $data]);
    }
}
