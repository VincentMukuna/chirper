import InputError from "@/Components/InputError.jsx";
import PrimaryButton from "@/Components/PrimaryButton.jsx";
import {useForm} from "@inertiajs/react";

export default function ReplyForm({chirp}){
    const {data, setData, post, processing, reset, errors} = useForm({
        message: '',
    })

    const submit = (e) => {
        e.preventDefault();
        post(route('chirps.reply', {'chirp':chirp.id}), {
            onSuccess: () => {
                reset('message');
            }
        });
    }
    return (
        <form onSubmit={submit} className='flex flex-col' >
            <textarea
                value={data.message}
                placeholder={"Chirp what you think about this"}
                onChange={(e) => setData('message', e.target.value)}
                className="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            >
            </textarea>
            <InputError error={errors.message} className="mt-2"/>
            <PrimaryButton className="mt-4 self-end" disabled={processing}>Reply</PrimaryButton>
        </form>
    )
}
