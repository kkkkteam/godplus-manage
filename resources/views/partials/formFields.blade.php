<div class="form-group">
 {!! Form::label($field['name'].':',null, ['class'=>'font-weight-bold']) !!}
 
 <!---- text ---->
 @if($field['type'] == 'text')
    @if(in_array($field['name'], $requiredFields))
     <!-- required filed sign -->
     <small style="color:red">*</small>
     {!! Form::text(
        $field['name'], 
        $field['value'] ?? null, 
        ['required'=>'required','class'=>'form-control']) 
     !!}
    @else
      {!! Form::text($field['name'], $field['value'] ?? null, ['class'=>'form-control']) !!}
    @endif

 <!---- text area ---->
 @elseif($field['type'] == 'textarea')
    @if(in_array($field['name'], $requiredFields))
     <!-- required filed sign -->
     <small style="color:red">*</small>
     {!!  Form::textarea(
        $field['name'], 
        $field['value'] ?? null, 
        ['required'=>'required', 'class' => 'form-control']) 
     !!}
    @else
     {!! Form::textarea($field['name'], $field['value'] ?? null, ['class' => 'form-control']) !!}
    @endif

 <!---- select ---->
 @elseif($field['type'] == 'select')
    @if(in_array($field['name'], $requiredFields))
     <!-- required filed sign -->
     <small style="color:red">*</small>
     {!! Form::select(
        $field['name'], 
        [null=>'select Type']+$field['options'], 
        $field['value'] ?? null, 
        ['required'=>'required', 'class' => 'form-control' ]) 
     !!}
    @else
     {!! Form::select(
        $field['name'], 
        [null=>'Select']+$field['options'], 
        $field['value'] ?? null, 
        ['class' => 'form-control']) 
     !!}
    @endif

 <!---- checkbox ---->
 @elseif($field['type'] == 'checkbox')
    @if(in_array($field['name'], $requiredFields))
     <!-- required filed sign -->
     <small style="color:red">*</small>
    @endif
    @foreach($field['options'] as $optionKey=>$optionValue)
       {!! Form::checkbox(
          $field['name'].'[]',
          $optionKey, 
          in_array($optionKey,$field['value'])) 
       !!} {{ $optionValue }}
    @endforeach

 <!---- radio button ---->
 @elseif($field['type'] == 'radiobutton')
    @if(in_array($field['name'], $requiredFields))
       <!-- required filed sign -->
       <small style="color:red">*</small>
    @endif
    @if(isset($field['option_1']))
       {!! Form::radio($field['name'],0, !$field['value']) !!} {{ $field['option_1'] }}
    @endif
    @if(isset($field['option_2']))
       {!! Form::radio($field['name'],1,$field['value']) !!} {{ $field['option_2'] }}
    @endif

 <!---- file ---->
 @elseif($field['type'] == 'file')
    @if(in_array($field['name'], $requiredFields))
     <!-- required filed sign -->
     <small style="color:red">*</small>
    @endif
    @if($field['multiple'] == 'no')
       {!! Form::file($field['name'], ['class'=>'btn btn-sml btn-default']) !!}
       <!-- show image -->
       @if(isset($field['value']))
          <img src="{{asset('assets/images'.'/'.$field['value'])}}" alt="" width="50" height="50">
       @endif
    @else 
       {!! Form::file($field['name'].'[]', ['multiple'=>true, 'class'=>'btn btn-sml btn-default']) !!}
       <!-- show image -->
       @if(!empty($field['value']))
          @foreach($field['value'] as $imageName)
           <img src="{{asset('assets/images'.'/'.$imageName)}}" alt="" width="50" height="50">
          @endforeach
       @endif
    @endif
@endif
</div>