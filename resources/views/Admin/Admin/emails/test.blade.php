@extends('layouts.admin_layout')
@section('content')
<section class="content-header">
  <h1>Test Form</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					{{ Form::model($email, ['url' => ['admin/test/form',$email],'files'=>true]) }}
						{{ Form::label('email', 'E-Mail Address')}}
						{{Form::text('email', '' ,['placeholder'=>'Enter Email'])}}
						{{Form::file('image')}}
						{{Form::number('name', 'value')}}	
						{{Form::checkbox('name', 'value')}}
						{{Form::radio('name', 'value')}}
						{{Form::checkbox('name', 'value', true)}}	
						{{Form::radio('name', 'value', true)}}
						<?php
							echo Form::select('size', array('L' => 'Large', 'S' => 'Small'));
							echo Form::select('size', array('L' => 'Large', 'S' => 'Small'), 'S'); 
							echo Form::select('animal', array(
									'Cats' => array('leopard' => 'Leopard'),
									'Dogs' => array('spaniel' => 'Spaniel'),
								));
							echo Form::selectRange('number', 10, 20);
							echo Form::selectMonth('month');
							echo link_to('foo/bar', 'hello', $attributes = array(), $secure = null);
							echo Form::submit('Click Me!');
						?>
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>
</section>

@endsection		  