@extends('layout')
@section('content')
    <div id="app">
        <div v-if="inProcess" style="z-index: 10;">
            <x-loader></x-loader>
        </div>

        <div class='step-one'>
            <div class='first-info'>
                <h5>Upload your ID photo</h5>
                <div class='text'>
                    <p>Please take a clear photo of your <b>driver licence (both sides), passport or proof of age card.</b></p>
                    <br/>
                    <p>Please avoid photos where your face or ID are blurred, out of focus, obscured or cropped off.</p>
                    <p><b>Don't take selfies in PORTRAIT camera mode</b></p>
                    <br/>
                    <p>A photo will be rejected if your face or information on your ID are not clearly visible.</p>
                </div>
            </div>
            <div class='second-info'>
                <div v-if="errors" class="alert alert-danger alert-dismissible fade show" role="alert" style="z-index: 9;position: fixed;top: 20px;right: 20px;left: 20px;">
                    <strong>Error !</strong> <span v-html="errors"></span>
                    <button @click="errors = null" type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <div class="flex-around">
                    <div style="position: relative; max-width: 50%">
                        <div v-if="previewOneLoader">
                            <x-img-loader></x-img-loader>
                        </div>

                        <div class='label'>Front side of the ID Card</div>
                        <div class='box-image'>
                            <img class='image' :src="previewOne ?? '/card-front.jpg'" alt="idcard"/>
                        </div>
                        <div class='box-upload'>
                            <input ref="fileInputOne" v-on:change="handleFileChange($event,'front')" type="file" accept="image/*" style="display: none"/>
                            <button class='button-input' @click="triggerFileInputOne">Click to upload</button>
                        </div>
                    </div>
                    <div style="position: relative; max-width: 50%">
                        <div v-if="previewTwoLoader">
                            <x-img-loader></x-img-loader>
                        </div>
                        <div class='label'>Back side of the ID Card</div>
                        <div class='box-image' style='display: none'>
                            <img class='image' :src="previewTwo ?? '/card-back.jpg'" alt="idcardback"/>
                        </div>
                        <div class='box-upload'>
                            <input ref="fileInputTwo" v-on:change="handleFileChange($event,'back')" type="file" accept="image/*" style="display: none"/>
                            <button class='button-input' @click="triggerFileInputTwo">Click to upload</button>
                        </div>
                    </div>
                </div>
                <div class='second-info'>
                    <div class='label' style="text-align: center">Selfie</div>
                    <div class='box-image' style="align-items: normal !important;position: relative">
                        <div v-if="previewThreeLoader">
                            <x-img-loader></x-img-loader>
                        </div>
                        <img class='image' :src="previewThree ?? '/selfie.jpg'" alt="idcard"/>
                    </div>
                    <div  class='box-upload'>
                        <input ref="fileInputThree" v-on:change="handleFileChange($event,'selfie')" type="file" accept="image/*" style="display: none"/>
                        <button class='button-input' @click="triggerFileInputThree">Click to upload</button>
                    </div>
                    <ul class='list'>
                        <li>Keep a neutral expression</li>
                        <li>Remove dark glasses or tinted lenses</li>
                        <li>Ensure both eyes are open</li>
                        <li>Take a well-lighted photo</li>
                        <li>No filters needed</li>
                        <li>No photos of photos</li>
                    </ul>
                </div>

                <ul class='list'>
                    <li>Use a color image not a black and white</li>
                    <li>Show all four corners of document</li>
                    <li>Make sure your face shows</li>
                    <li>Don`t use photocopies or screenshots</li>
                </ul>
                <div class='rules'>
                    <p><span>ACCEPTED IDENTIFICATION</span></p>
                    <p>We`ll accept only valid documents (passports, ids, etc.).</p>
                    <p>Foreign passports must be current and, if not written in English, will need to be translated by an accredited translator.</p>
                    <p>We’ll accept an original document or certified copy of the translation.</p>
                    <p>A Proof of Age card must be issued by an Australian state or territory with your name, photo and signature.</p>
                </div>
            </div>
            <button :disabled="!(previewOne && previewTwo && previewThree)" class="my-button primary" @click="stepRegistration()">
                Submit
            </button>
        </div>
    </div>

    <script type="module">
        import { saveData, getData, openDatabase } from '/js/IndexedDB.js';
        const {createApp, ref, onMounted} = Vue
        window.hash = "{{ request('hash') }}";
        window.hallId = "{{ request('hallId') }}";
        window.sessionId = "{{ session()->getId() }}";
        window.registration = "{{ route('digital.registration', ['hallId' => request('hallId'), 'hash' => request('hash')], false) }}";

        createApp({
            setup() {
                const errors = ref(null);
                const inProcess = ref(false);
                const previewOne = ref(null);
                const previewOneLoader = ref(false);
                const previewTwo = ref(null);
                const previewTwoLoader = ref();
                const previewThree = ref(null);
                const previewThreeLoader = ref(false);
                const fileInputOne = ref(null);
                const fileInputTwo = ref(null);
                const fileInputThree = ref(null);
                const hash = ref(window.hash);
                const hallId = ref(window.hallId);
                const sessionId = ref(window.sessionId);
                const registration = ref(window.registration);
                const data = ref({
                    'hash': hash.value,
                    'hallId': hallId.value,
                    'sessionId': sessionId.value,
                    'front': null,
                    'back': null,
                    'selfie': null
                })

                onMounted(() => {
                    document.getElementById('app').style.opacity = 1

                    openDatabase().then(() => {
                        previewOneLoader.value = true
                        previewTwoLoader.value = true
                        previewThreeLoader.value = true
                        getData('st_1' + sessionId.value)
                            .then((data) => {
                                if(data){
                                    previewOne.value = JSON.parse(data.data).preview
                                }
                                previewOneLoader.value = false
                            })
                            .catch((error) => {
                                console.error('Error fetching data:', error);
                            });

                        getData('st_2' + sessionId.value)
                            .then((data) => {
                                if(data){
                                    previewTwo.value = JSON.parse(data.data).preview
                                }
                                previewTwoLoader.value = false
                            })
                            .catch((error) => {
                                console.error('Error fetching data:', error);
                            });

                        getData('st_3' + sessionId.value)
                            .then((data) => {
                                if(data){
                                    previewThree.value = JSON.parse(data.data).preview
                                }
                                previewThreeLoader.value = false
                            })
                            .catch((error) => {
                                console.error('Error fetching data:', error);
                            });
                    }).catch((error) => {
                        console.error('Error initializing the database:', error);
                    });
                })

                const triggerFileInputOne = () => {
                    fileInputOne.value.click();
                };

                const triggerFileInputTwo = () => {
                    fileInputTwo.value.click();
                };

                const triggerFileInputThree = () => {
                    fileInputThree.value.click();
                };

                const handleFileChange = async (event,side) => {

                    if(side === 'front'){
                        previewOneLoader.value = true
                    }
                    if(side === 'back'){
                        previewTwoLoader.value = true
                    }
                    if(side === 'selfie'){
                        previewThreeLoader.value = true
                    }

                    const selectedFile = event.target.files[0];
                    if (selectedFile) {
                        const reader = new FileReader();
                        const readFile = () => {
                            return new Promise((resolve, reject) => {
                                reader.onload = () => resolve(reader.result);
                                reader.onerror = reject;
                            });
                        };

                        readFile()
                            .then((result) => {
                                if(side === 'front'){
                                    previewOne.value = result;
                                    data.value.front = selectedFile

                                    let stepOne = {id: sessionId.value, preview: previewOne.value};
                                    saveData('st_1'+sessionId.value, JSON.stringify(stepOne))

                                    previewOneLoader.value = false
                                }
                                if(side === 'back'){
                                    previewTwo.value = result;
                                    data.value.back = selectedFile

                                    let stepTwo = {id: sessionId.value, preview: previewTwo.value};
                                    saveData('st_2'+sessionId.value, JSON.stringify(stepTwo))

                                    previewTwoLoader.value = false
                                }
                                if(side === 'selfie'){
                                    previewThree.value = result;
                                    data.value.selfie = selectedFile;

                                    let stepThree = {id: sessionId.value, preview: previewThree.value};
                                    saveData('st_3'+sessionId.value, JSON.stringify(stepThree))

                                    previewThreeLoader.value = false
                                }
                            })
                            .catch((error) => {
                                console.error('Ошибка при чтении файла', error);
                                previewOneLoader.value = false
                                previewTwoLoader.value = false
                                previewThreeLoader.value = false
                            });
                        reader.readAsDataURL(selectedFile);
                    }

                };

                const stepRegistration = () => {
                        window.location.href = registration.value
                }

                return {
                    fileInputOne,fileInputTwo,fileInputThree,previewOne,previewTwo,previewThree,errors,
                    inProcess,previewOneLoader,previewTwoLoader,previewThreeLoader,
                    triggerFileInputThree, triggerFileInputOne, triggerFileInputTwo,
                    handleFileChange, stepRegistration
                }
            }
        }).mount('#app')
    </script>

    <style>
        #app {
            opacity: 0;
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
        }
        .flex-around {
            display: flex;
            justify-content: space-around;
        }
        .flex-center {
            display: flex;
            justify-content: center;
        }
    </style>
@endsection
