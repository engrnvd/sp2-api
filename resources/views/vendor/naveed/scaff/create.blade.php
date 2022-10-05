<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>

<template>
    <b-modal id="create{{$table->studly(true)}}Modal"
             ref="create{{$table->studly(true)}}Modal"
             title="Add New {{$table->title(true)}}"
             hide-footer
             modal-class="modal-right">
        <apm-form action="/{{$table->slug()}}" v-model="form">
@foreach($table->fields as $field)
            <apm-form-element field="{{$field->name}}" label="{{$field->title()}}" :model="form">
@if($field->type === 'enum' && count($field->enumValues) < 3)
                <b-form-radio-group class="pt-2" :options="['{!! join("', '", $field->enumValues) !!}']" v-model="form.{{$field->name}}"/>
@elseif($field->type === 'text')
                <b-textarea v-flex-height v-model="form.{{$field->name}}" :rows="2" :max-rows="2"/>
@elseif($field->type === 'enum')
                <v-select :options="['{!! join("', '", $field->enumValues) !!}']" v-model="form.{{$field->name}}"/>
@elseif($field->type === 'boolean')
                <switches v-model="form.{{$field->name}}" theme="custom" color="primary"></switches>
@else
                <b-form-input v-model="form.{{$field->name}}"/>
@endif
            </apm-form-element>
@endforeach
            <b-button variant="outline-secondary" @click="$refs.create{{$table->studly(true)}}Modal.hide()" class="mr-2">Cancel</b-button>
            <b-button variant="primary" type="submit" class="mr-1">Save</b-button>
        </apm-form>
    </b-modal>
</template>

<script>
    export default {
        name: "Create{{$table->studly(true)}}Modal",
        props: {},
        data() {
            return {
                form: {
@foreach($table->fields as $field)
                    {{$field->name}}: '{{ $field->default ?? '' }}',
@endforeach
                }
            }
        },
        methods: {}
    }
</script>

<style scoped>

</style>

