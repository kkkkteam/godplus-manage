<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChurchMember;

class ChurchMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    //-----------------------------------------------------------
    {
        //member-form
    }

    /**
     * Show the form for creating a new resource.
     */
    //-----------------------------------------------------------
    public function create()
    {
        $member = new ChurchMember();
        $fields = $this->generateMemberFields($member);
        $requiredFields = $this->requiredFields;

        return view('join_member', compact('fields', 'requiredFields'));
    }

    /**
     * Store a newly created resource in storage.
     */
    //-----------------------------------------------------------
    public function store(Request $request)
    {
        $validator = $this->validateInput($request);
        if($validator->fails())
            return redirect()->back()->withErrors($validator)->withInput();

        $member = new ChurchMember();
        $member = $this->saveData($request, $member);

        if(!$member->save())
            return back()->with('error', 'Failed saving member.')->withInput();

        return view('member_created');
    }

    /**
     * Display the specified resource.
     */
    //-----------------------------------------------------------
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    //-----------------------------------------------------------
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    //-----------------------------------------------------------
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    //-----------------------------------------------------------
    public function destroy(string $id)
    {
        //
    }

    //-----------------------------------------------------------
    private $requiredFields = [
        'surname_zh',
        'lastname_zh',
        'mobile',
        'birthday',
        'school_type',
        'gender',
    ];

    //-----------------------------------------------------------
    private function generateMemberFields($member){

        $fields = [
            ['name' => 'nickname', 'type'=> 'text', 'value'=> $member->nickname],
            ['name' => 'surname_en', 'type'=> 'text', 'value'=> $member->surname_en],
            ['name' => 'lastname_en', 'type'=> 'text', 'value'=> $member->lastname_en],
            ['name' => 'surname_zh', 'type'=> 'text', 'value'=> $member->surname_zh],
            ['name' => 'lastname_zh', 'type'=> 'text', 'value'=> $member->lastname_zh],
            ['name'=> 'mobile', 'type'=> 'text', 'value'=> $member->mobile],
            ['name' => 'birthday','type' => 'date', 'value'=>$member->birthday],
            [
                'name' => 'gender', 
                'type'=> 'select', 
                'value'=> $member->gender ? 'male' : 'female',
                'options'=>['male'=>'男生', 'female'=>'女生']
            ],
            // [
            //     'name' => 'is_combined',
            //     'type' => 'radiobutton', 
            //     'value'=> $member->is_combined ? 'option_2' : 'option_1', 
            //     'option_1' => 'No',
            //     'option_2' => 'Yes',
            // ],
            // [
            //     'name' => 'facilities',
            //     'type' => 'checkbox', 
            //     'value'=>explode(',', $member->facilities), 
            //     'options' => ['rest_room'=>'Rest Room', 'prayer_room'=>'Prayer Room', 'medical_room'=>'Medical Room'],
            // ],
            // ['name'=> 'logo', 'type' => 'file', 'value'=> $member->logo, 'multiple'=> 'no'],
            // [
            //     'name'=> 'gallery', 
            //     'type' => 'file', 
            //     'multiple'=> 'yes', 
            //     'value'=>array_filter(explode(',', $member->gallery))
            // ]
        ];

        return $fields;
    }

    private function validateInput(Request $request){
        return Validator::make(
            $request->all(),
            [
                // 'name' => 'required',
                // 'info' => 'required',
                // 'address' => 'required',
                // 'stublished_on' => 'required',
                // 'school_type' => 'required',
                // 'is_combined' => 'required',
                // 'logo' => 'required|image|mimes:jpeg,png,jpg|max:2048',

                'surname_zh'=> 'required',
                'lastname_zh'=> 'required',
                'mobile'=> 'required',
                'birthday'=> 'required',
                'school_type'=> 'required',
                'gender'=> 'required',
            ]
        );
    }

    private function saveData(Request $request, $member){

        // $galleryImages = [];
        // if(isset($request->gallery)){
        //     foreach ($request->gallery as $image) {
        //         $fileName = $this->saveImage($image);
        //         if(!$fileName)
        //             return back()->with('error', 'Failed saving Logo.')->withInput();
        //         $galleryImages[] = $fileName;  
        //     }
        // }

        // $logoImage = $this->saveImage($request->logo);
        // if(!$logoImage)
        //     return back()->with('error', 'Failed saving Logo.')->withInput();

        $member->nickname = $request->nickname ?? "";
        $member->surname_en = $request->surname_en ?? "";
        $member->lastname_en = $request->lastname_en ?? "";
        $member->surname_zh = $request->surname_zh;
        $member->lastname_zh = $request->lastname_zh;
        $member->birthday = Carbon::parse($request->birthday);
        $member->gender = $request->gender;
        $member->mobile = $request->mobile;

        return $member;
    }


}
