export default function Button({ children, as, color, ...props }) {
    const Elememt = as ?? "div";

    const colors = {
        indigo: 'bg-indigo-600 hover:bg-indigo-500 text-white',
        red: 'bg-red-600 hover:bg-red-500 text-white',
        gray: 'bg-gray-100 text-gray-800 hover:bg-gray-300'
    };

    return(
        <Elememt className={`${colors[color]} rounded-md px-3 py-2 text-sm font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500`}
                 {...props}
        >
            {children}
        </Elememt>
    )
}