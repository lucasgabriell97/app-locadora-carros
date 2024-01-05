<template>
    <div>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="(t, key) in titulos" :key="key">
                        {{ t.titulo }}
                    </th>
                    <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel">Ações</th>
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
                    <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel">
                        <button
                            v-if="visualizar.visivel" 
                            class="btn btn-outline-info btn-sm" 
                            :data-bs-toggle="visualizar.dataBsToggle" 
                            :data-bs-target="visualizar.dataBsTarget"
                        >
                            <font-awesome-icon icon="fa-solid fa-eye" />
                        </button>
                        <button  
                            class="btn btn-outline-primary btn-sm"
                        >
                            <font-awesome-icon icon="fa-solid fa-pencil" />
                        </button>
                        <button  
                            class="btn btn-outline-danger btn-sm"
                        >
                            <font-awesome-icon icon="fa-solid fa-trash" />
                        </button>
                    </td> 
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ["dados", "titulos", "visualizar"],
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

button {
    margin-right: 0.3rem;
}

@media only screen and (max-width: 500px) {
    button {
        margin-block: 0.3rem;
    }
}
</style>
