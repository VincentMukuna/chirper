import {createContext, useContext} from "react";
import {cn} from "@/lib/utils.js";
import {Link, router, usePage} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton.jsx";

const UserContext=createContext();


export function User({user, children, className}){
    return(
        <UserContext.Provider value={{user}}>
            <article className={cn('flex p-6 gap-3 items-center hover:bg-indigo-50 transition-all', className)}>
                {children}
            </article>
        </UserContext.Provider>
    )
}

const useUser = ()=>useContext(UserContext);

export function UserAvatar({className}){
    const {user} = useUser();
    return(
        <div className={cn('bg-gray-600 rounded-full w-10 h-10', className)}>

        </div>
    )
}

export function UserDetails({className}){
    const {user} = useUser();
    return(
        <div className={cn('flex flex-col gap-1', className)}>
            <Link
                href={route('users.show', [user.id])}
                className={'font-semibold hover:underline'}
            >{user.name}
            </Link>
            <span className={'line-clamp-1'}>{user.bio}</span>
        </div>
    )
}

export function UserActions({className}){
    const{auth} = usePage().props
    const {user} = useUser();
    const onToggleFollow=()=>{
        router.post(
            route('users.toggle-follow', {user:user.id}),
            {},
            {
                preserveScroll:true,
                preserveState:true,
            })
    }
    return(
        <div className={cn('ms-auto', className)}>
            {user.id === auth.user.id
                ? null :
                <PrimaryButton
                    aria-label={'toggle following'}
                    title = {user.isFollow?"Unfollow user":"null"}
                    className={cn('', {
                            'bg-transparent border-2 border-gray-600 text-gray-800 hover:text-white':user.isFollow,
                            '':!user.isFollow
                        }
                    )}
                    onClick={() => onToggleFollow()}
                >
                    {user.isFollow ? "Following" : "Follow"}
                </PrimaryButton>
            }
        </div>

    )

}
