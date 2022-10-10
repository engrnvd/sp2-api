<?php
/* @var $table \Naveed\Scaff\Helpers\Table */
/* @var $gen \Naveed\Scaff\Generators\ModelGenerator */
?>

import { defineStore } from 'pinia'
import { FetchRequest } from '@/helpers/fetch-request'
import { useNotify } from '@/U/composables/Notifiy'

const notify = useNotify()
const form = {
@foreach ( $table->fields as $field )
    {{$field->name}}: '',
@endforeach
}

export const use{{$table->studly()}}Store = defineStore('{{$table->camel()}}', {
  state: () => ({
    form: { ...form },
    req: new FetchRequest('{{$table->slug()}}', 'GET').withProps({
      pagination: true,
      delay: 500,
      params: {
        sort: '{{$table->idField}}',
        sortType: 'desc',
      },
    }),
    createReq: new FetchRequest('{{$table->slug()}}', 'POST')
  }),
  getters: {},
  actions: {
    load() {
      this.req.send()
    },
    create() {
      return this.createReq.send({
        body: JSON.stringify(this.form)
      }).then(res => {
        this.req.data = this.req.data || { data: [] }
        // @ts-ignore
        this.req.data.data = this.req.data.data || []
        // @ts-ignore
        this.req.data.data.unshift(res)
        this.resetForm()

        notify.success('Success', '{{$table->title(true)}} created')
      })
    },
    resetForm() {
      this.form = { ...form }
    },
  },
})
