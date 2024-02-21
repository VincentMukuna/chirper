import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import {Head, usePage} from "@inertiajs/react";
import ReplyForm from "@/Pages/Chirps/Partials/ReplyForm.jsx";
import {Chirp, ChirpActions, ChirpAvatar, ChirpBody, ChirpContent, ChirpHeader} from "@/Components/Chirp.jsx";

export default function Show({chirp, auth}){
    return(
        <AuthenticatedLayout user={auth.user}>
            <Head title={`Chirp by ${chirp.user.name}: ${chirp.message}`}/>
            <main className='max-w-2xl mx-auto p-4 sm:p-6 lg:p-8'>
                <div className='p-4 bg-white shadow-sm rounded-lg '>
                    <Chirp chirp={chirp}>
                        <ChirpAvatar />
                        <ChirpContent>
                            <ChirpHeader />
                            <ChirpBody />
                            <ChirpActions/>
                        </ChirpContent>
                    </Chirp>
                    <div className='ms-12 my-4'>
                        <ReplyForm chirp={chirp}/>
                    </div>

                    <div className='flex flex-col border-l ms-12'>
                        {chirp.replies.map(reply=>(
                            <Chirp key={reply.id} chirp={reply}>
                                <ChirpAvatar />
                                <ChirpContent>
                                    <ChirpHeader />
                                    <ChirpBody />
                                </ChirpContent>
                            </Chirp>
                        ))}
                    </div>

                </div>
            </main>
        </AuthenticatedLayout>

    )
}
