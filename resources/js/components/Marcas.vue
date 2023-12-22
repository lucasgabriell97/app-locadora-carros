<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component titulo="Busca de marcas">
                    <template v-slot:conteudo>
                        <div class="row">
                            <div class="col mb-3">
                                <input-container-component
                                    titulo="ID"
                                    id="inputId"
                                    id-help="idHelp"
                                    texto-ajuda="Opcional. Informe o id do registro"
                                >
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="inputId"
                                        aria-describedby="idHelp"
                                        placeholder="ID"
                                    />
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component
                                    titulo="Nome da marca"
                                    id="inputNome"
                                    id-help="nomeHelp"
                                    texto-ajuda="Opcional. Informe o nome do registro"
                                >
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="inputNome"
                                        aria-describedby="nomeHelp"
                                        placeholder="Nome da marca"
                                    />
                                </input-container-component>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm">
                            Pesquisar
                        </button>
                    </template>
                </card-component>

                <card-component titulo="Relação de marcas">
                    <template v-slot:conteudo>
                        <table-component 
                            :dados="marcas" 
                            :titulos="{
                                id: {titulo: 'ID', tipo: 'texto'}, 
                                nome: {titulo: 'Nome', tipo: 'texto'}, 
                                imagem: {titulo: 'Imagem', tipo: 'imagem'},
                                created_at: {titulo: 'Criação', tipo: 'data'}
                            }"
                        ></table-component>
                    </template>

                    <template v-slot:rodape>
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#modalMarca"
                        >
                            Adicionar
                        </button>
                    </template>
                </card-component>
            </div>
        </div>
        <modal-component id="modalMarca" titulo="Adicionar marca">
            <template v-slot:alertas>
                <alert-component
                    tipo="success"
                    :detalhes="transacaoDetalhes"
                    v-if="transacaoStatus == 'adicionado'"
                    titulo="Cadastro realizado com sucesso"
                ></alert-component>
                <alert-component
                    tipo="danger"
                    :detalhes="transacaoDetalhes"
                    v-if="transacaoStatus == 'erro'"
                    titulo="Erro ao cadastrar a marca"
                ></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group mb-3">
                    <input-container-component
                        titulo="Nome da marca"
                        id="novoNome"
                        id-help="novoNomeHelp"
                        texto-ajuda="Informe o nome da marca"
                    >
                        <input
                            type="text"
                            class="form-control"
                            id="novoNome"
                            aria-describedby="novoNomeHelp"
                            placeholder="Nome da marca"
                            v-model="nomeMarca"
                        />
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component
                        titulo="Imagem"
                        id="novoImagem"
                        id-help="novoImagemHelp"
                        texto-ajuda="Selecione uma imagem no formato PNG"
                    >
                        <input
                            type="file"
                            class="form-control"
                            id="novoImagem"
                            aria-describedby="novoImagemHelp"
                            placeholder="NSelecione uma imagem"
                            @change="carregarImagem($event)"
                        />
                    </input-container-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Fechar
                </button>
                <button type="button" class="btn btn-primary" @click="salvar()">
                    Salvar
                </button>
            </template>
        </modal-component>
    </div>
</template>

<script>
export default {
    computed: {
        token() {
            let token = document.cookie.split(";").find((i) => {
                return i.includes("token=");
            });

            token = token.split("=")[1];
            token = "Bearer " + token;

            return token;
        },
    },
    data() {
        return {
            urlBase: "http://localhost:8000/api/v1/marca",
            nomeMarca: "",
            arquivoImagem: [],
            transacaoStatus: {},
            transacaoDetalhes: [],
            marcas: []
        };
    },
    methods: {
        carregarImagem(e) {
            this.arquivoImagem = e.target.files;
        },
        listar() {
            let config = {
                headers: {
                    Accept: "application/json",
                    Authorization: this.token,
                },
            };

            axios.get(this.urlBase, config)
                .then((res) => {
                    this.marcas = res.data
                })
                .catch((errors) => {
                    console.log(errors)
                })
        },
        salvar() {
            let formData = new FormData();
            formData.append("nome", this.nomeMarca);
            formData.append("imagem", this.arquivoImagem[0]);

            let config = {
                headers: {
                    "Content-Type": "multipart/form-data",
                    Accept: "application/json",
                    Authorization: this.token,
                },
            };

            axios.post(this.urlBase, formData, config)
                .then((res) => {
                    this.transacaoStatus = "adicionado"
                    this.transacaoDetalhes = {
                        mensagem: `ID do registro: ${res.data.id}`
                    }

                    // Swal.fire({
                    //     title: "Cadastro realizado com sucesso!",
                    //     text: "Do you want to continue",
                    //     icon: "success",
                    //     confirmButtonText: "OK",
                    // });

                    console.log(res);
                })
                .catch((errors) => {
                    this.transacaoStatus = "erro"
                    this.transacaoDetalhes = {
                        dados: errors.response.data.errors
                    }
                });
        },
    },
    mounted() {
        this.listar()
    }
};
</script>
