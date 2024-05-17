<template>

    <div>
        <textarea-input :value="value" @input="update" />
    </div>

</template>

<script>
export default {

    mixins: [Fieldtype],

    data() {
        return {
            //
        };
    }

};
</script>
