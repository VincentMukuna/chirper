import { forwardRef, useEffect, useRef } from 'react';
import IconChatBubble, {IconSearch, IconUserFollows} from "@/Components/Icons.jsx";

export default forwardRef(function SearchInput({ type = 'search', className = '', isFocused = false, ...props }, ref) {
    const input = ref ? ref : useRef();

    useEffect(() => {
        if (isFocused) {
            input.current.focus();
        }
    }, []);

    return (
        <div className={'relative flex items-center'}>
            <IconSearch className={'absolute start-2 w-5 h-5 opacity-70'} />
            <input
                {...props}
                type={type}
                className={
                    'ps-8 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm ' +
                    className
                }
                ref={input}
            />
        </div>

    );
});
