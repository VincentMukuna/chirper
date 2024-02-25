import React from "react";
import {Head, useForm} from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import InputError from "@/Components/InputError.jsx";
import PrimaryButton from "@/Components/PrimaryButton.jsx";
import {Chirp, ChirpActions, ChirpAvatar, ChirpBody, ChirpContent, ChirpHeader} from "@/Components/Chirp.jsx";



export default function Index({auth, chirps}) {
    const {data, setData, post, processing, reset, errors} = useForm({
        message: '',
    });

    console.log("CHIRPS: ", chirps)

    const submit = (e) => {
        e.preventDefault();
        post(route('chirps.store'), {
            onSuccess: () => {
                reset('message');
            }
        });
    }

    return(
        <AuthenticatedLayout user={auth.user}>
            <Head title="Chirps" />

            <div className = "max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
                <form onSubmit={submit}>
                    <textarea
                        value={data.message}
                        required
                        placeholder={"What's on your mind"}
                        onChange={(e) => setData('message', e.target.value)}
                        className="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                    >
                    </textarea>
                    <InputError error={errors.message} className="mt-2" />
                    <PrimaryButton className="mt-4" disabled={processing}>Chirp</PrimaryButton>
                </form>
                <div className="mt-6 bg-white shadow-sm rounded-lg divide-y">
                    {chirps.data.map((chirp) => (
                        <Chirp key={chirp.id} chirp={chirp}>
                            <ChirpAvatar />
                            <ChirpContent>
                                <ChirpHeader />
                                <ChirpBody />
                                <ChirpActions/>
                            </ChirpContent>
                        </Chirp>
                    ))
                    }
                </div>
            </div>
        </AuthenticatedLayout>
    )
}
