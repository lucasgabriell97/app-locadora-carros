<template>
    <div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="(t, key) in titulos" :key="key">
                        {{ t.titulo }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(obj, key) in dadosFiltrados" :key="key">
                   <td v-for="(valor, key) in obj" :key="key">
                        <span v-if="titulos[key].tipo == 'texto'">{{ valor }}</span>
                        <span v-if="titulos[key].tipo == 'data'">{{ $filters.formatDate(valor) }}</span>
                        <span v-if="titulos[key].tipo == 'imagem'">
                            <img :src="`/storage/${valor}`" />
                        </span>
                    </td> 
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ["dados", "titulos"],
    computed: {
        dadosFiltrados() {
            let campos = Object.keys(this.titulos)
            let dadosFiltrados = []

            this.dados.map((item, key) => {
                let itemFiltrado = {}
                campos.forEach(campo => {
                    itemFiltrado[campo] = item[campo]
                })
                dadosFiltrados.push(itemFiltrado)
            })
 
            return dadosFiltrados
        }
    }
};
</script>

<style scoped>
img {
    width: 2rem;
    height: 2rem;
}
</style>
