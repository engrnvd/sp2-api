<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */

?>
<script setup lang="ts">
    import { use{{$table->studly()}}Store } from '@/views/{{$table->slug()}}/store'
    import CloudDownloadIcon from '@/material-design-icons/CloudDownload.vue'
    import ReloadIcon from '@/material-design-icons/Reload.vue'
    import PlusIcon from '@/material-design-icons/Plus.vue'
    import UIconBtn from '@/U/components/UIconBtn.vue'
    import ApmFilter from '@/components/common/crud/ApmFilter.vue'
    import ApmEditable from '@/components/common/crud/ApmEditable.vue'
    import NotFoundRow from '@/components/common/crud/NotFoundRow.vue'
    import ApmDeleteBtn from '@/components/common/crud/ApmDeleteBtn.vue'
    import ApmPagination from '@/components/common/crud/ApmPagination.vue'
    import MainLoader from '@/components/common/MainLoader.vue'
    import { onMounted, watch } from 'vue'
    import { useRouter, RouterView } from 'vue-router'

    const router = useRouter()
    const {{$table->camel()}} = use{{$table->studly()}}Store()

    onMounted(() => {
        if (!{{$table->camel()}}.req.hasLoadedData) {{$table->camel()}}.load()
    })

    watch(() => {{$table->camel()}}.req.params, () => {
        {{$table->camel()}}.load()
    }, { deep: true })
</script>

<template>
    <div>
        <RouterView/>
        <div class="d-flex align-items-center gap-2 px-4">
            <div class="flex-grow-1">
                <h2>{{$table->title()}}</h2>
            </div>
            <UIconBtn tooltip="Create a new {{$table->title(true)}}" @click="router.push('/{{$table->slug()}}/create')">
                <PlusIcon/>
            </UIconBtn>
            <UIconBtn tooltip="Download CSV" @click.prevent="{{$table->camel()}}.load()">
                <CloudDownloadIcon/>
            </UIconBtn>
            <UIconBtn
                :loading="{{$table->camel()}}.req.loading"
                tooltip="Reload"
                @click.prevent="{{$table->camel()}}.load()">
                <ReloadIcon/>
            </UIconBtn>
        </div>
        <div class="card p-4" style="min-height: 30em">
            <table class="w100 table-hover crud-table">
                <thead>
                <tr>
                    <th>ID</th>
@foreach($table->fields as $field)
                    <th>
                        <ApmFilter
                            field-name="{{$field->name}}"
                            field-label="{{$field->title()}}"
                            v-model="{{$table->camel()}}.req.params"
                        />
                    </th>
@endforeach
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="{{$table->camel(true)}} in {{$table->camel()}}.req.data.data" :key="{{$table->camel(true)}}.{{$table->idField}}">
                    <td>{{'{{'}} {{$table->camel(true)}}.{{$table->idField}} }}</td>
@foreach($table->fields as $field)
                    <td>
                        <ApmEditable
                            type="{{$gen->getEditableType($field)}}"
                            field="{{$field->name}}"
                            :url="`/{{$table->slug()}}/{{'${'}}{{$table->camel(true)}}.{{$table->idField}}}`"
                            v-model="{{$table->camel(true)}}.{{$field->name}}"
                        ></ApmEditable>
                    </td>
@endforeach
                    <td>
                        <ApmDeleteBtn :req="{{$table->camel()}}.req" :{{$table->idField}}="{{$table->camel(true)}}.{{$table->idField}}"/>
                    </td>
                </tr>
                <NotFoundRow :req="{{$table->camel()}}.req"/>
                </tbody>
            </table>
            <MainLoader v-if="{{$table->camel()}}.req.loading"/>
            <ApmPagination class="mt-4" :req="{{$table->camel()}}.req"/>
        </div>
    </div>
</template>
