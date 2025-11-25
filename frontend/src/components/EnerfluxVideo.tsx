import React from "react";

type EnerfluxLogoProps = React.SVGProps<SVGSVGElement>;

const EnerfluxLogo: React.FC<EnerfluxLogoProps> = (props) => {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 200 100"
            width="100%"
            height="auto"
            {...props}
        >
            {/* Fondo del logo */}
            <rect width="200" height="100" rx="15" fill="#0f3460" />

            {/* Sol */}
            <circle cx="40" cy="30" r="15" fill="#FFD700" className="icon-sun" />
            <line x1={40} y1={15} x2={40} y2={5} stroke="#FFD700" strokeWidth={2} />
            <line x1={40} y1={45} x2={40} y2={55} stroke="#FFD700" strokeWidth={2} />
            <line x1={25} y1={30} x2={15} y2={30} stroke="#FFD700" strokeWidth={2} />
            <line x1={55} y1={30} x2={65} y2={30} stroke="#FFD700" strokeWidth={2} />
            <line x1={30} y1={20} x2={20} y2={10} stroke="#FFD700" strokeWidth={2} />
            <line x1={50} y1={20} x2={60} y2={10} stroke="#FFD700" strokeWidth={2} />
            <line x1={30} y1={40} x2={20} y2={50} stroke="#FFD700" strokeWidth={2} />
            <line x1={50} y1={40} x2={60} y2={50} stroke="#FFD700" strokeWidth={2} />

            {/* Panel solar */}
            <rect x={65} y={20} width={30} height={20} fill="#4A90E2" className="icon-panel" />
            <line x1={65} y1={25} x2={95} y2={25} stroke="#3A7BD5" strokeWidth={1} />
            <line x1={65} y1={30} x2={95} y2={30} stroke="#3A7BD5" strokeWidth={1} />
            <line x1={65} y1={35} x2={95} y2={35} stroke="#3A7BD5" strokeWidth={1} />

            {/* Batería */}
            <rect x={110} y={20} width={20} height={20} fill="#4A90E2" className="icon-battery" />
            <rect x={115} y={15} width={10} height={5} fill="#4A90E2" />
            <path d="M118,25 L122,25 L120,30 Z" fill="#FFD700" />

            {/* Texto */}
            <text
                x={100}
                y={70}
                fontFamily="Arial"
                fontSize={18}
                fontWeight="bold"
                fill="white"
                textAnchor="middle"
            >
                ENERFLUX
            </text>
            <text
                x={100}
                y={85}
                fontFamily="Arial"
                fontSize={10}
                fill="#BBBBBB"
                textAnchor="middle"
            >
                GESTIÓN Y MONITORIZACIÓN SOLAR
            </text>
        </svg>
    );
};

export default EnerfluxLogo;
