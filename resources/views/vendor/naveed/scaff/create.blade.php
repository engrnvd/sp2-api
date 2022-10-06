<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>

<script setup lang="ts">
    import UModal from '@/U/components/UModal.vue'
    import UInput from '@/U/components/UInput.vue'
    import { requiredRule } from '@/Vee/rules/required.rule'
    import { useValidator } from '@/Vee/useValidator'
    import { Validator } from '@/Vee/validator'
    import { use{{$table->studly()}}Store } from '@/views/{{$table->slug()}}/store'
    import { useRouter } from 'vue-router'

    const router = useRouter()
    const {{$table->camel()}} = use{{$table->studly()}}Store()

    const v = useValidator({{$table->camel()}}.form, (v: Validator) => {
        @foreach($table->fields as $field)
        @if ($field->required && !in_array($field->name, config('naveed-scaff.skipped-fields')))
        v.addRule(requiredRule('{{$field->name}}'))
        @endif
        @if (preg_match("/email/", $field->name) && !in_array($field->name, config('naveed-scaff.skipped-fields')))
        v.addRule(emailRule('{{$field->name}}'))
        @endif
        @endforeach
    })

    function save() {
        v.validate()
        if (v.hasErrors) return
        {{$table->camel()}}.create().then(res => {
            router.back()
        })
    }

</script>

<template>
    <UModal
        title="Add New {{$table->title(true)}}"
        :model-value="true"
        @cancel="router.back()"
        ok-title="Save"
        @ok="save"
        :ok-loading="{{$table->camel()}}.createReq.loading"
        cancel-title="Back"
        size="sm"
    >
        <form @submit.prevent="save">
@foreach($table->fields as $field)
    @if($field->type === 'enum' && count($field->enumValues) < 3)
        todo: Render radio: <input type="radio">
    @elseif($field->type === 'text')
        <textare>todo</textare>
    @elseif($field->type === 'enum')
        <select>
        @foreach($field->enumValues as $v)
            <option>{{$v}}</option>
        @endforeach
        </select>
    @elseif($field->type === 'boolean')
        todo: switch
    @else
            <UInput
                v-model="{{$table->camel()}}.form.{{$field->name}}"
                label="{{$field->title()}}"
                :errors="v.errors.{{$field->name}}"
                class="mb-4"
            />
    @endif
@endforeach
        </form>
    </UModal>
</template>
