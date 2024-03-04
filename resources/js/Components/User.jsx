import {createContext, useContext, useState} from "react";
import {cn} from "@/lib/utils.js";
import {Link, router, usePage} from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton.jsx";
import Highlight from "@/Components/Highlight.jsx";
import SecondaryButton from "@/Components/SecondaryButton.jsx";


const UserContext=createContext({user: null});


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
        <div className={cn('bg-gray-600 rounded-full w-10 h-10 shrink-0', className)}>

        </div>
    )
}

export function UserDetails({className,highlight, showBio=true}){
    const {user} = useUser();
    return(
        <div className={cn('flex flex-col gap-1', className)}>
            <Link
                href={route('users.show', [user.id])}
                className={'font-semibold hover:underline'}
            >
                {highlight?
                    <Highlight text={user.name} highlight={highlight}/>
                    :user.name
                }

            </Link>
            {showBio&&<span className={'line-clamp-1'}>{user.bio}</span>}
        </div>
    )
}

export function UserActions({className}){
    const{auth} = usePage().props
    const {user} = useUser();

    const [isFollowing, setIsFollowing] = useState(user.is_following)
    const onToggleFollow=()=>{
        router.post(
            route('users.toggle-follow', {user:user.id}),
            {},
            {
                preserveScroll:true,
                preserveState:true,

            })
        setIsFollowing(!isFollowing)
    }
    return(
        <div className={cn('ms-auto', className)}>
            {user.id === auth.user.id
                ? null :
                isFollowing
                    ?
                    <SecondaryButton
                        onClick={() => onToggleFollow()}
                    >
                        Unfollow
                    </SecondaryButton>
                    :
                    <PrimaryButton
                        onClick={() => onToggleFollow()}

                    >
                        Follow
                    </PrimaryButton>

            }
        </div>

    )

}
