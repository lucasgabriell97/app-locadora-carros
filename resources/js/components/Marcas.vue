<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- Início do card de busca -->
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
                                        v-model="busca.id"
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
                                        v-model="busca.nome"
                                    />
                                </input-container-component>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm" @click="pesquisar()">
                            Pesquisar
                        </button>
                    </template>
                </card-component>
                <!-- Fim do card de busca -->

                <!-- Início do card de marcas -->
                <card-component titulo="Relação de marcas">
                    <template v-slot:conteudo>
                        <div v-if="nenhumRegistro" class="alert alert-secondary text-center">
                            Nenhum registro encontrado!
                        </div>
                        <table-component
                            :dados="marcas.data"
                            :visualizar="{
                                visivel: true, 
                                dataBsToggle: 'modal', 
                                dataBsTarget: '#modalMarcaVisualizar'
                            }"
                            atualizar="true"
                            remover="true"
                            :titulos="{
                                id: { titulo: 'ID', tipo: 'texto' },
                                nome: { titulo: 'Nome', tipo: 'texto' },
                                imagem: { titulo: 'Imagem', tipo: 'imagem' },
                                created_at: { titulo: 'Criação', tipo: 'data' },
                            }"
                        ></table-component>
                    </template>

                    <template v-slot:rodape>
                        <div class="d-flex justify-content-between align-items-center">

                            <paginate-component>
                                <li v-for="(l, key) in marcas.links" :key="key" 
                                    :class="l.active ? 'page-item active' : 'page-item'" 
                                    @click="paginacao(l)"
                                >
                                    <a class="page-link" v-html="obterLabelSemPreviousNext(l.label)"></a>
                                </li>
                            </paginate-component>

                            <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#modalMarca"
                            >
                                Adicionar
                            </button>
                        </div>
                        <div class="col"></div>
                    </template>
                </card-component>
                <!-- Fim do card de marcas -->
            </div>
        </div>

        <!-- Início do modal de inclusão de marca -->
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
                            placeholder="Selecione uma imagem"
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
        <!-- Fim do modal de inclusão de marca -->

        <!-- Início do modal de visualização de marca -->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar marca">
            <template v-slot:alertas>

            </template>
            <template v-slot:conteudo>
                Teste
            </template>
            <template v-slot:rodape>
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Fechar
                </button>
            </template>
        </modal-component>
        <!-- Fim do modal de visualização de marca -->
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
            urlPaginacao: '',
            urlFiltro: '',
            nomeMarca: '',
            nenhumRegistro: false,
            arquivoImagem: [],
            transacaoStatus: {},
            transacaoDetalhes: [],
            marcas: { 
                data: [],
                links: [
                    { label: '&laquo; Previous' },
                    { label: 'Next &raquo;' }
                ] 
            },
            busca: {
                id: '',
                nome: ''
            }
        };
    },
    methods: {
        carregarImagem(e) {
            this.arquivoImagem = e.target.files;
        },
        obterLabelSemPreviousNext(label) {
            return label.replace('&laquo; Previous', '&laquo').replace('Next &raquo;', '&raquo'); 
        },
        paginacao(l) {
            if(l.url != null) {
                this.urlPaginacao = l.url.split('?')[1]
                this.listar()
            }
        },
        pesquisar() {
            let filtro = ''

            for(let key in this.busca) {
                if(this.busca[key]) {
                    if(filtro != '') {
                        filtro += ";"
                    }
                    filtro += `${key}:like:${this.busca[key]}`
                }
            }
            if(filtro != '') {
                this.urlPaginacao = 'page=1'
                this.urlFiltro = `&filtro=${filtro}`
            } else {
                this.urlFiltro = ''
            }
            this.listar()
        },
        listar() {
            let url = `${this.urlBase}?${this.urlPaginacao}${this.urlFiltro}`
            let config = {
                headers: {
                    Accept: "application/json",
                    Authorization: this.token,
                },
            };

            axios
                .get(url, config)
                .then((res) => {
                    this.nenhumRegistro = res.data.data.length === 0
                    this.marcas = res.data;
                })
                .catch((errors) => {
                    console.log(errors);
                });
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

            axios
                .post(this.urlBase, formData, config)
                .then((res) => {
                    this.transacaoStatus = "adicionado";
                    this.transacaoDetalhes = {
                        mensagem: `ID do registro: ${res.data.id}`,
                    };

                    // Swal.fire({
                    //     title: "Cadastro realizado com sucesso!",
                    //     text: "Do you want to continue",
                    //     icon: "success",
                    //     confirmButtonText: "OK",
                    // });

                    console.log(res);
                })
                .catch((errors) => {
                    this.transacaoStatus = "erro";
                    this.transacaoDetalhes = {
                        dados: errors.response.data.errors,
                    };
                });
        },
    },
    mounted() {
        this.listar();
    },
};
</script>
